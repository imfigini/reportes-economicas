DROP PROCEDURE "dba".sp_extensiones_seguimiento();

CREATE PROCEDURE "dba".sp_extensiones_seguimiento()
       RETURNING VARCHAR(255), INTEGER, INTEGER, INTEGER, INTEGER, INTEGER, INTEGER, INTEGER; 	

DEFINE vc_materia_nombre VARCHAR(255);
DEFINE i_aprobados INTEGER;
DEFINE i_reprobados INTEGER;
DEFINE i_no_se_presentaron INTEGER;
DEFINE i_libres INTEGER;
DEFINE i_no_se_presentaron_nunca INTEGER;
DEFINE i_no_se_les_vencio INTEGER;
DEFINE i_total INTEGER;

DEFINE vc_materia VARCHAR(5); 
DEFINE i_cant INTEGER;

BEGIN	

-------Calculo los alumnos que aprobaron el final dentro de la fecha de prórrga de la extensión

	SELECT DISTINCT materia, legajo, MAX(f_prorroga_hasta) AS f_prorroga, resultado_final, fecha_final
	FROM rep_extensiones_cursada
		WHERE resultado_final = 'A'
	GROUP BY 1,2,4,5
	INTO TEMP aux_aprobados;

	SELECT DISTINCT A.materia, COUNT(A.legajo) AS cantidad_aprobados
		FROM aux_aprobados A
		WHERE A.fecha_final < A.f_prorroga
	GROUP BY materia
	INTO TEMP ret_aprobados;

	
-------Calculo los alumnos que reprobaron (y no aprobaron) el final dentro de la fecha de prórrga de la extensión

	SELECT DISTINCT materia, legajo, MAX(f_prorroga_hasta) AS f_prorroga, resultado_final, MAX(fecha_final) AS fecha_final
	FROM rep_extensiones_cursada
		WHERE resultado_final = 'R' 	
			AND f_prorroga_hasta > fecha_final
			AND f_venc_reg_ant < fecha_final
			AND legajo||materia NOT IN (SELECT legajo||materia FROM aux_aprobados
							WHERE rep_extensiones_cursada.materia = aux_aprobados.materia
								AND rep_extensiones_cursada.legajo = aux_aprobados.legajo
								AND aux_aprobados.fecha_final < aux_aprobados.f_prorroga)
	GROUP BY 1,2,4
	INTO TEMP auxi;

	SELECT DISTINCT materia, COUNT(legajo) AS cantidad_reprobados
		FROM auxi
		WHERE fecha_final < f_prorroga
	GROUP BY materia
	INTO TEMP ret_reprobados;

	DROP TABLE auxi;
	
-------Calculo los alumnos que no se presentaron a final dentro de la fecha de prórrga de la extensión

	SELECT DISTINCT R1.materia, R1.legajo, MAX(R1.f_prorroga_hasta) AS f_prorroga, R1.resultado_final, R1.fecha_final
	FROM rep_extensiones_cursada R1
		WHERE R1.f_prorroga_hasta < TODAY
			AND R1.legajo||R1.materia NOT IN (SELECT R2.legajo||R2.materia FROM rep_extensiones_cursada R2
								WHERE R2.legajo = R1.legajo
									AND R2.materia = R1.materia
									AND (	(R2.resultado_final = 'R' AND R2.f_venc_reg_ant < R2.fecha_final AND R2.fecha_final < R2.f_prorroga_hasta)
										OR (R2.resultado_final = 'A' AND R2.f_venc_reg_ant < R2.fecha_final AND R2.fecha_final < R2.f_prorroga_hasta)	)
							)
	GROUP BY 1,2,4,5
	INTO TEMP auxi;

	SELECT DISTINCT materia, COUNT(legajo) AS cantidad_no_se_presentaron
		FROM auxi
	GROUP BY materia
	INTO TEMP ret_no_se_presentaron;

	DROP TABLE auxi;
	
-------Calculo los alumnos que aprobaron el final fuera de la fecha de prórrga de la extensión

	SELECT DISTINCT A.materia, COUNT(A.legajo) AS cantidad_libres
		FROM aux_aprobados A
		WHERE A.fecha_final > A.f_prorroga
	GROUP BY materia
	INTO TEMP ret_aprobados_libre;

-------Calculo los alumnos que no se han presentado nunca y que ya se les venció la prórroga de extensión

	SELECT DISTINCT materia, legajo, MAX(f_prorroga_hasta) AS f_prorroga, resultado_final, fecha_final
	FROM rep_extensiones_cursada
		WHERE f_prorroga_hasta < TODAY
		AND resultado_final IS NULL
	GROUP BY 1,2,4,5
	INTO TEMP auxi;

	SELECT DISTINCT materia, COUNT(legajo) AS cantidad_no_se_presentaron_nunca
		FROM auxi
	GROUP BY materia
	INTO TEMP ret_no_se_presentaron_nunca;

	DROP TABLE auxi;


-------Calculo los alumnos que aún no se les ha venido la prórroga de la extensión y aún no la han aprobado

	SELECT DISTINCT materia, legajo, MAX(f_prorroga_hasta) AS f_prorroga, resultado_final, MAX(fecha_final) AS fecha_final
	FROM rep_extensiones_cursada
		WHERE f_prorroga_hasta > TODAY
		AND legajo||materia NOT IN (SELECT legajo||materia FROM aux_aprobados A
							WHERE A.fecha_final < A.f_prorroga
								AND A.materia = rep_extensiones_cursada.materia
								AND A.legajo = rep_extensiones_cursada.legajo)
	GROUP BY 1,2,4
	INTO TEMP auxi;

	SELECT DISTINCT materia, COUNT(legajo) AS cantidad_no_se_les_vencio
		FROM auxi
	GROUP BY materia
	INTO TEMP ret_no_se_les_vencio;

	DROP TABLE auxi;

	
-------Calculo el total de pedidos que tuvo cada materia

	SELECT DISTINCT materia, legajo
		FROM rep_extensiones_cursada 
	INTO TEMP auxi;

	SELECT materia, COUNT(legajo) AS cant_total
		FROM auxi
	GROUP BY materia
	INTO TEMP ret_totales;
	
	DROP TABLE auxi;
	
-------Armo la respuesta

CREATE TABLE respuesta
(	materia VARCHAR(5),
	cant_aprobados INTEGER,
	cant_reprobados INTEGER,
	cant_no_se_presentaron INTEGER,
	cant_aprobados_libre INTEGER,
	cant_no_se_presentaron_nunca INTEGER,
	cant_no_se_les_vencio INTEGER,
	total INTEGER
);

INSERT INTO respuesta (materia) SELECT DISTINCT materia FROM rep_extensiones_cursada;

FOREACH 
	SELECT materia, cantidad_aprobados 
	INTO vc_materia, i_cant
	FROM ret_aprobados

	UPDATE respuesta SET cant_aprobados = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cantidad_reprobados
	INTO vc_materia, i_cant
	FROM ret_reprobados

	UPDATE respuesta SET cant_reprobados = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cantidad_no_se_presentaron
	INTO vc_materia, i_cant
	FROM ret_no_se_presentaron

	UPDATE respuesta SET cant_no_se_presentaron = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cantidad_libres
	INTO vc_materia, i_cant
	FROM ret_aprobados_libre

	UPDATE respuesta SET cant_aprobados_libre = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cantidad_no_se_presentaron_nunca
	INTO vc_materia, i_cant
	FROM ret_no_se_presentaron_nunca

	UPDATE respuesta SET cant_no_se_presentaron_nunca = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cantidad_no_se_les_vencio
	INTO vc_materia, i_cant
	FROM ret_no_se_les_vencio

	UPDATE respuesta SET cant_no_se_les_vencio = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

FOREACH 
	SELECT materia, cant_total
	INTO vc_materia, i_cant
	FROM ret_totales

	UPDATE respuesta SET total = i_cant WHERE respuesta.materia = vc_materia;
END FOREACH;

----------Retorno resultado
FOREACH
	SELECT M.nombre, cant_aprobados, cant_reprobados, cant_no_se_presentaron, cant_aprobados_libre, cant_no_se_presentaron_nunca, cant_no_se_les_vencio, total
	INTO vc_materia_nombre, i_aprobados, i_reprobados, i_no_se_presentaron, i_libres, i_no_se_presentaron_nunca, i_no_se_les_vencio, i_total
	FROM respuesta R, sga_materias M
		WHERE R.materia = M.materia
	RETURN vc_materia_nombre, i_aprobados, i_reprobados, i_no_se_presentaron, i_libres, i_no_se_presentaron_nunca, i_no_se_les_vencio, i_total WITH RESUME;
END FOREACH;

DROP TABLE aux_aprobados;
DROP TABLE ret_aprobados;
DROP TABLE ret_reprobados;
DROP TABLE ret_no_se_presentaron;
DROP TABLE ret_aprobados_libre;
DROP TABLE ret_no_se_presentaron_nunca;
DROP TABLE ret_no_se_les_vencio;
DROP TABLE ret_totales;
DROP TABLE respuesta; 

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_extensiones_seguimiento();
