/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  imfigini
 * Created: 13/08/2018
 */

DROP PROCEDURE 'dba'.sp_rep_desaprobados(integer);

CREATE PROCEDURE "dba".sp_rep_desaprobados(v_anio INT)

DEFINE vCarrera LIKE sga_alumnos.carrera;
DEFINE vLegajo  LIKE sga_alumnos.legajo;
DEFINE vMatemAprob  INT;
DEFINE vResolAprob  INT;
DEFINE vIVUAprob    INT;
DEFINE vMatemReprob  INT;
DEFINE vResolReprob  INT;
DEFINE vIVUReprob    INT;

BEGIN

LET vCarrera        = NULL;
LET vLegajo         = NULL;

----------------------------------------------
--Inscriptos que reprobaron el curso de ingreso (por no haber aprobado las 3 materias)
{
--DROP TABLE dba.rep_desaprobados;
CREATE TABLE dba.rep_desaprobados (
	sede VARCHAR(5),
	legajo VARCHAR(10),
	apellido VARCHAR (30),
	nombres VARCHAR(30),
	dni VARCHAR(15),
	carrera VARCHAR(5),
	carrera_nombre VARCHAR(100),
	anio_ingreso int,
	e_mail VARCHAR(50),
        veces_reprob_matem INTEGER,
        veces_reprob_resol INTEGER,
        veces_reprob_ivu INTEGER
);
--DROP INDEX idx_rep_desaprobados;
--CREATE INDEX idx_rep_desaprobados ON rep_desaprobados (legajo);
}

DELETE FROM rep_desaprobados WHERE anio_ingreso = v_anio;

SELECT DISTINCT     S.sede,
		    A.legajo, 
                    P.apellido, 
                    P.nombres,
                    P.nro_documento AS dni, 
                    A.carrera, 
                    C.nombre AS carrera_nombre,
                    PI.anio_academico AS anio_ingreso, 
                    D.e_mail
            FROM sga_periodo_insc PI 
            JOIN sga_carrera_aspira CA ON (CA.periodo_inscripcio = PI.periodo_inscripcio)
            JOIN sga_alumnos A ON (A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
	    JOIN sga_sedes S ON (A.sede = S.sede)
            JOIN sga_personas P ON (P.nro_inscripcion = A.nro_inscripcion)
            LEFT JOIN vw_datos_censales_actuales D ON (D.unidad_academica = P.unidad_academica AND D.nro_inscripcion = P.nro_inscripcion)
            JOIN sga_carreras C ON (C.carrera = A.carrera)
                WHERE 	PI.anio_academico = v_anio
    INTO TEMP aux_inscriptos WITH NO LOG;

--ALTER INDEX idx_rep_desaprobados TO cluster;

    FOREACH
        SELECT legajo, carrera 
            INTO vLegajo, vCarrera
        FROM aux_inscriptos WHERE anio_ingreso = v_anio
                
        LET vMatemAprob    = 0;
        LET vResolAprob    = 0;
        LET vIVUAprob      = 0;

        SELECT COUNT(*) 
            INTO vMatemAprob
        FROM vw_hist_academica 
            WHERE legajo = vLegajo
            AND carrera = vCarrera
            AND resultado = 'A'
            AND materia = '001' ;

        SELECT COUNT(*) 
            INTO vResolAprob
        FROM vw_hist_academica 
            WHERE legajo = vLegajo
            AND carrera = vCarrera
            AND resultado = 'A'
            AND materia = '003' ;

        SELECT COUNT(*) 
            INTO vIVUAprob
        FROM vw_hist_academica 
            WHERE legajo = vLegajo
            AND carrera = vCarrera
            AND resultado = 'A'
            AND materia = '002' ;

        IF (vMatemAprob = 0 OR vResolAprob = 0 OR vIVUAprob = 0) THEN 
        BEGIN
            INSERT INTO rep_desaprobados (sede, legajo, apellido, nombres, dni, carrera, carrera_nombre, anio_ingreso, e_mail)
                SELECT sede, legajo, apellido, nombres, dni, carrera, carrera_nombre, anio_ingreso, e_mail 
                    FROM aux_inscriptos
			WHERE 	legajo = vLegajo
				AND carrera = vCarrera;
            
	    --Matematica
            IF (vMatemAprob = 1) THEN 
                UPDATE rep_desaprobados SET veces_reprob_matem = -1 WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            ELSE
                LET vMatemReprob = 0;
                EXECUTE PROCEDURE "dba".sp_rep_veces_reprobo_materia(v_anio, vLegajo, vCarrera, '001') INTO vMatemReprob;
                UPDATE rep_desaprobados SET veces_reprob_matem = vMatemReprob WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            END IF;
            
            --Resolución de Problemas
            IF (vResolAprob = 1) THEN 
                UPDATE rep_desaprobados SET veces_reprob_resol = -1 WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            ELSE
                LET vResolReprob = 0;
                EXECUTE PROCEDURE "dba".sp_rep_veces_reprobo_materia(v_anio, vLegajo, vCarrera, '003') INTO vResolReprob;
                UPDATE rep_desaprobados SET veces_reprob_resol = vResolReprob WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            END IF;

            --IVU
            IF (vIVUAprob = 1) THEN 
                UPDATE rep_desaprobados SET veces_reprob_ivu = -1 WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            ELSE
                LET vIVUReprob = 0;
                EXECUTE PROCEDURE "dba".sp_rep_veces_reprobo_materia(v_anio, vLegajo, vCarrera, '002') INTO vIVUReprob;
                UPDATE rep_desaprobados SET veces_reprob_ivu = vIVUReprob WHERE legajo = vLegajo AND carrera = vCarrera AND anio_ingreso = v_anio;
            END IF;

        END;
        END IF;
    END FOREACH;

    DROP TABLE aux_inscriptos; 
    INSERT INTO rep_fecha_actualiz_tablas VALUES ('rep_desaprobados', CURRENT YEAR TO SECOND);

END;
END PROCEDURE;