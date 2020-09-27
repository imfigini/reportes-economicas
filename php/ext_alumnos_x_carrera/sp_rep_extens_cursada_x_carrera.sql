DROP FUNCTION 'dba'.sp_rep_extens_cursada_x_carrera();

CREATE PROCEDURE "dba".sp_rep_extens_cursada_x_carrera()
RETURNING VARCHAR(255), INTEGER, INTEGER, INTEGER, FLOAT(5);

DEFINE vc_carrera LIKE sga_carreras.nombre;
DEFINE i_anio	INTEGER;
DEFINE i_cant_extensiones INTEGER;
DEFINE i_cant_alumnos_totales INTEGER;
DEFINE f_porcentaje FLOAT(5);

BEGIN	

	--Tomo los alumnos que solicitaron extendion por año de solicitud
	SELECT DISTINCT carrera, YEAR(fecha_alta) AS anio_solicitud, legajo
		FROM rep_extensiones_cursada
	INTO TEMP tmp_extensiones;

	--Cuento los alumnos que solicitaron extension por año
	SELECT carrera, anio_solicitud, COUNT(legajo) AS cant_alu_extensiones
		FROM tmp_extensiones
	GROUP BY 1,2
	INTO TEMP tmp_cant_extensiones;

	--Retorno la cantidad de alumnos que solicitaron extensiones y la cantidad de alumnos regulares por carrera y por año
	FOREACH
		SELECT C.nombre, R.anio, E.cant_alu_extensiones, COUNT(R.legajo) AS cant_alu_regulares,  (E.cant_alu_extensiones*100/COUNT(R.legajo))::FLOAT(5) AS porcentaje
		INTO vc_carrera, i_anio, i_cant_extensiones, i_cant_alumnos_totales, f_porcentaje
			FROM tmp_cant_extensiones E, rep_alumnos_regulares R, sga_carreras C
			WHERE 	E.carrera = R.carrera
				AND E.anio_solicitud = R.anio
				AND E.carrera = C.carrera
		GROUP BY 1,2,3

		RETURN vc_carrera, i_anio, i_cant_extensiones, i_cant_alumnos_totales, f_porcentaje WITH RESUME; 

	END FOREACH;

	DROP TABLE tmp_extensiones;
	DROP TABLE tmp_cant_extensiones;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_extens_cursada_x_carrera();
