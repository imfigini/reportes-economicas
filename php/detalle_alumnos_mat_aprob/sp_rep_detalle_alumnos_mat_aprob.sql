DROP PROCEDURE 'dba'.sp_rep_detalle_alumnos_mat_aprob();

CREATE PROCEDURE "dba".sp_rep_detalle_alumnos_mat_aprob()

-- VARIABLES PARA EL MANEJO DE EXCEPCIONES.
DEFINE SQLErr              INTEGER;
DEFINE ISAMError           INTEGER;
DEFINE errorInfo           VARCHAR(76);

-- SI HAY ALGÚN ERROR.
ON EXCEPTION SET SQLErr, ISAMError, errorInfo                                        
   BEGIN
      -- BORRO LAS TABLAS SI EXISTEN
      ON EXCEPTION IN (-206)
      END EXCEPTION WITH RESUME;
      DROP TABLE rep_detalle_alumnos_mat_aprob;
   END
   RAISE EXCEPTION SQLErr, ISAMError, errorInfo;
END EXCEPTION;


BEGIN

{
   DROP TABLE rep_detalle_alumnos_mat_aprob;
   CREATE TABLE rep_detalle_alumnos_mat_aprob 
   (
	legajo		VARCHAR(15),
	carrera 	VARCHAR(5),
	plan 		VARCHAR(5),
	anio_ingreso	INTEGER,
	materia 	VARCHAR(5),
	fecha_regularidad	DATE,
	forma_aprob_cursada	VARCHAR(2),
	fecha_examen		DATE,	
	forma_aprob_final	VARCHAR(25)
   );
}

   DELETE FROM rep_detalle_alumnos_mat_aprob;

   INSERT INTO rep_detalle_alumnos_mat_aprob 
	SELECT C.legajo, C.carrera, C.plan, YEAR(A.fecha_ingreso), C.materia, C.fecha_regularidad, C.origen, V.fecha, V.forma_aprobacion
	FROM sga_alumnos A 
	JOIN sga_cursadas C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera AND A.legajo = C.legajo AND A.regular = 'S' AND A.calidad = 'A' AND C.resultado IN ('A', 'P')
	LEFT JOIN vw_hist_academica V ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo AND C.materia = V.materia AND V.resultado = 'A')
	WHERE A.regular = 'S' AND A.calidad = 'A';

   INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_detalle_alumnos_mat_aprob', CURRENT YEAR TO SECOND);

END;

END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_detalle_alumnos_mat_aprob();
--SELECT * FROM rep_detalle_alumnos_mat_aprob;
