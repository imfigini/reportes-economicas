<?php
require_once('MisConsultas.php');

class ci_requisito4 extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function get_alumnos_con_50porc_carrera()
	{
		
		$db = MisConsultas::getConexion ();
		$sql = "SELECT R.legajo, R.alumno, R.nombre_carrera, R.porcentaje, D.e_mail
					FROM rep_porcentaje_carrera R, vw_datos_censales_actuales D
						WHERE R.porcentaje >= 50
							AND R.legajo = D.nro_inscripcion
							AND R.legajo NOT IN (SELECT legajo FROM sga_alumnos WHERE calidad = 'E')";
		$resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		
		$max = count($resultado);
		for ($i=0; $i<$max; $i++)
		{
			$num_aleatorio = rand(1,5000);
			$resultado[$i]['NUM_ALEAT'] = $num_aleatorio;
		}
//		ei_arbol($resultado);
		return $resultado;
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_alumnos_con_50porc_carrera();
		$cuadro->set_datos($datos);
	}


	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE sp_alumnos_prox_recibirse()";
		$db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_porcentaje_carrera'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}
}

?>