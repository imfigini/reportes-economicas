<?php
require_once('MisConsultas.php');

class ci_alumnos_cursadas_tit_interm extends toba_ci
{

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$sql = "SELECT  legajo, 
						alumno, 
						e_mail, 
						carrera, 
						nombre_carrera, 
						plan
				FROM rep_alumnos_con_cursadas_tit_interm
				ORDER BY alumno";
	
		$db = MisConsultas::getConexion ();
		$datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_alumnos_con_cursadas_tit_interm'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE sp_rep_alumnos_con_cursadas_tit_interm()";
        $db->query($sql)->fetchAll();
    }        
}

?>