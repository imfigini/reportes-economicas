/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  imfigini
 * Created: 13/08/2018
 */

DROP PROCEDURE 'dba'.sp_rep_veces_reprobo_materia(int, char, char, char);

CREATE PROCEDURE "dba".sp_rep_veces_reprobo_materia(
			v_anio int, 
			vLegajo LIKE sga_alumnos.legajo, 
			vCarrera LIKE sga_carreras.carrera, 
			vMateria LIKE sga_materias.materia)
RETURNING INT;

DEFINE v_reprobo_final INT;
DEFINE v_reprobo_cursada INT;

BEGIN             

LET v_reprobo_final = 0;
LET v_reprobo_cursada = 0;

    SELECT COUNT(*)
        INTO v_reprobo_final
                FROM vw_hist_academica 
                    WHERE   legajo = vLegajo
                            AND carrera = vCarrera
                            AND materia = vMateria
                            AND resultado = 'R';

    SELECT COUNT(*)
        INTO v_reprobo_cursada
		FROM sga_cursadas
                    WHERE   legajo = vLegajo
                            AND carrera = vCarrera
                            AND materia = vMateria
                            AND resultado = 'R';

    RETURN v_reprobo_final + v_reprobo_cursada;

END;
END PROCEDURE;

--EXECUTE PROCEDURE "dba".sp_rep_veces_reprobo_materia(2018, 'EXA-236553', 206, '001');