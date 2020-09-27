DROP PROCEDURE 'dba'.sp_rep_solo_falta_tesis();

CREATE PROCEDURE "dba".sp_rep_solo_falta_tesis()

DEFINE v_ua			LIKE sga_carreras.unidad_academica;
DEFINE v_legajo			LIKE sga_alumnos.legajo; 
DEFINE v_carrera		LIKE sga_carreras.carrera;
DEFINE d_fecha_ingreso		DATE; 
DEFINE d_fecha_ult_actividad	DATE;

DEFINE solo_falta_tesis		INTEGER;


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
      DROP TABLE rep_solo_falta_tesis;
   END
   RAISE EXCEPTION SQLErr, ISAMError, errorInfo;
END EXCEPTION;


BEGIN

{
   DROP TABLE rep_solo_falta_tesis;
   CREATE TABLE rep_solo_falta_tesis 
   (
	legajo			VARCHAR(15),
	carrera 		VARCHAR(5),
	fecha_ingreso		DATE,
	fecha_ult_actividad 	DATE
   );
}

	DELETE FROM rep_solo_falta_tesis;

	--Selecciono alumnos no egresados, regulares y activos

	FOREACH 
		SELECT unidad_academica, legajo, carrera
			INTO v_ua, v_legajo, v_carrera
			FROM sga_alumnos
				WHERE calidad = 'A'
	
		EXECUTE PROCEDURE sp_solo_falta_tesis(v_ua, v_carrera, v_legajo) INTO solo_falta_tesis;
		
		IF (solo_falta_tesis = 1) THEN 

			SELECT  A.fecha_ingreso, MAX(V.fecha) AS fecha_ult_actividad
				INTO d_fecha_ingreso, d_fecha_ult_actividad
			FROM sga_alumnos A, vw_hist_academica V
			WHERE	A.unidad_academica = v_ua
				AND A.legajo = v_legajo
				AND A.carrera = v_carrera
				AND A.unidad_academica = V.unidad_academica
				AND A.legajo = V.legajo
				AND A.carrera = V.carrera
				AND V.resultado = 'A'
			GROUP BY 1;

			INSERT INTO rep_solo_falta_tesis VALUES (v_legajo, v_carrera, d_fecha_ingreso, d_fecha_ult_actividad);
		END IF;
	END FOREACH;
	
	INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_solo_falta_tesis', CURRENT YEAR TO SECOND);

END;

END PROCEDURE;