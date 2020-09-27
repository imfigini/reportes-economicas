<?php
require_once('MisConsultas.php');
class consultas
{
	
    static function get_alumnos($filtro=array())
    {
        if (isset($filtro['carrera'])) 
                $carrera = $filtro['carrera'];
        if (isset($filtro['materia1'])) 
                $materia1 = $filtro['materia1'];
        if (isset($filtro['materia2'])) 
                $materia2 = $filtro['materia2'];

        $sql = "SELECT DISTINCT A.legajo, P.apellido || ', ' || P.nombres AS alumno, W.e_mail
                                FROM vw_hist_academica V
                                JOIN sga_alumnos A ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo)
                                JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                                JOIN vw_datos_censales_actuales W ON (P.unidad_academica = W.unidad_academica AND P.nro_inscripcion = W.nro_inscripcion)
                                WHERE V.resultado IN ('A', 'P')
                                AND V.materia = '$materia1'
                                AND A.carrera = $carrera
                                AND A.calidad = 'A' AND A.regular = 'S'
                                AND A.legajo IN 
                                (SELECT DISTINCT A.legajo
                                        FROM vw_hist_academica V
                                        JOIN sga_alumnos A ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo)
                                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                                        WHERE V.resultado IN ('A', 'P')
                                        AND A.carrera = $carrera
                                        AND V.materia = '$materia2'
                                        AND A.calidad = 'A' AND A.regular = 'S'
                                ) ";
        

        if (isset($filtro['materia3'])) 
        {
            $materia3 = $filtro['materia3'];
            
            $sql .= "AND A.legajo IN 
                                (SELECT DISTINCT A.legajo
                                        FROM vw_hist_academica V
                                        JOIN sga_alumnos A ON (A.unidad_academica = V.unidad_academica AND A.carrera = V.carrera AND A.legajo = V.legajo)
                                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                                        WHERE V.resultado IN ('A', 'P')
                                        AND A.carrera = $carrera
                                        AND V.materia = '$materia3'
                                        AND A.calidad = 'A' AND A.regular = 'S'
                                ) ";
        }

        $sql .= " ORDER BY alumno";

        $db = MisConsultas::getConexion ();
        $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $alumnos;
    }

}