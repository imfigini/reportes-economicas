DROP FUNCTION 'dba'.sp_rep_extens_cursada_x_mes();

CREATE PROCEDURE "dba".sp_rep_extens_cursada_x_mes()
RETURNING INTEGER, INTEGER, INTEGER, FLOAT(5);

DEFINE i_anio	INTEGER;
DEFINE i_mes	INTEGER;
DEFINE i_cant_extensiones INTEGER;
DEFINE f_porcentaje FLOAT(5);

BEGIN	

	--Tomo los alumnos que solicitaron extendion por año y mes de solicitud
	SELECT DISTINCT carrera, YEAR(fecha_alta) AS anio_solicitud, MONTH(fecha_alta) AS mes_solicitud, legajo
		FROM rep_extensiones_cursada
	INTO TEMP tmp_extensiones;

	--Cuento los alumnos que solicitaron extension por mes y año
	SELECT carrera, anio_solicitud, mes_solicitud, COUNT(legajo) AS cant_alu_extensiones
		FROM tmp_extensiones
	GROUP BY 1,2,3
	INTO TEMP tmp_cant_extensiones;

	--Cuento la cantidad de alumnos regulares por año
	SELECT anio, COUNT(legajo) AS cant_alu_regulares
		FROM rep_alumnos_regulares 
	GROUP BY 1
	INTO TEMP tmp_cant_regulares;

	--Retorno la cantidad de alumnos que solicitaron extensiones por mes y año, y el porcentaje que representa del total de alumnos
	FOREACH
		SELECT E.anio_solicitud, E.mes_solicitud, E.cant_alu_extensiones, (E.cant_alu_extensiones*100/cant_alu_regulares)::FLOAT(5) AS porcentaje
		INTO i_anio, i_mes, i_cant_extensiones, f_porcentaje
			FROM tmp_cant_extensiones E, tmp_cant_regulares R
			WHERE 	E.anio_solicitud = R.anio


		RETURN i_anio, i_mes, i_cant_extensiones, f_porcentaje WITH RESUME; 

	END FOREACH;

	DROP TABLE tmp_extensiones;
	DROP TABLE tmp_cant_extensiones;
	DROP TABLE tmp_cant_regulares;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_extens_cursada_x_mes();

