<?php
require_once('MisConsultas.php');

class ci_ext_x_anio_ingreso_y_avance extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	//Número de alumnos por carrera que solicitaron extensión y número total de alumnos por carrera (x año)
	function get_alu_x_carrera_solicitaron_ext()
	{
		$db = MisConsultas::getConexion();
		$sql = 'EXECUTE PROCEDURE "dba".sp_rep_extensiones_por_anio_plan()';
		$result = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_alu_x_carrera_solicitaron_ext();
		$cuadro->set_datos($datos);
	}
	
	//-----------------------------------------------------------------------------------
	//---- formulario_procesar ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario_procesar__procesar($datos)
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