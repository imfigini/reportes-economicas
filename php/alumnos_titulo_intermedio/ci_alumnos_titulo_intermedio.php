<?php
require_once('MisConsultas.php');

class ci_alumnos_titulo_intermedio extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            $datos = $this->get_alumnos_con_intermedio_sin_titulo();
            $cuadro->set_datos($datos);
	}

        function get_alumnos_con_intermedio_sin_titulo()
        {
            $sql = "SELECT  legajo, 
                            alumno, 
                            e_mail, 
                            carrera, 
                            nombre_carrera, 
                            plan, 
                            CASE 
                                WHEN regular = 'S' THEN 'Si'
                                WHEN regular = 'N' THEN 'No'
                            END AS regular, 
                            fecha_ingreso, 
                            fecha_ultima_actividad
                    FROM rep_alumnos_con_intermedio_sin_titulo
                    ORDER BY fecha_ultima_actividad";
                    
            $db = MisConsultas::getConexion ();
            $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        }
        
   //-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexion();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_alumnos_con_intermedio_sin_titulo'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
        $db = MisConsultas::getConexion();
        $sql = "EXECUTE PROCEDURE sp_rep_alumnos_con_intermedio_sin_titulo()";
        $db->query($sql)->fetchAll();
    }        
}