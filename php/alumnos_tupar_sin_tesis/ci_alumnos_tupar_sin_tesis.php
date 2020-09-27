<?php
require_once('MisConsultas.php');
class ci_alumnos_tupar_sin_tesis extends toba_ci
{
    //---- Cuadro -----------------------------------------------------------------------

    function get_alumnos_tupar_sin_tesis()
    {
        $sql = "SELECT A.legajo, P.apellido || ', ' || P.nombres AS alumno, C.e_mail, (19-COUNT(*))::INTEGER AS cant
                        FROM sga_alumnos A, vw_hist_academica V, sga_personas P, vw_datos_censales_actuales C
                        WHERE A.carrera = 212
                        AND A.regular = 'S' 
                        AND A.calidad = 'A'
                        AND A.unidad_academica = V.unidad_academica
                        AND A.carrera = V.carrera
                        AND A.legajo = V.legajo
                        AND V.resultado = 'A'
                        AND V.materia <> '0208'
                        AND A.unidad_academica = P.unidad_academica 
                        AND A.nro_inscripcion = P.nro_inscripcion
                        AND P.unidad_academica = C.unidad_academica
                        AND P.nro_inscripcion = C.nro_inscripcion
                GROUP BY 1, 2,3
                HAVING COUNT(*) >= 16
                ORDER BY 2";
        
        $db = MisConsultas::getConexion ();
        $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	return $resultado;
    }
    
    function conf__cuadro(toba_ei_cuadro $cuadro)
    {
        $datos = self::get_alumnos_tupar_sin_tesis();
        $cuadro->set_datos($datos);
    }

    function evt__cuadro__seleccion($datos)
    {
	$this->dep('datos')->cargar($datos);
    }

}
