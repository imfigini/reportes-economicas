<?php
require_once('MisConsultas.php');

class ci_ext_alumnos_x_carrera extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	//Número de alumnos por carrera que solicitaron extensión y número total de alumnos por carrera (x año)
	function get_alu_x_carrera_solicitaron_ext()
	{
		$db = MisConsultas::getConexion();
		$sql = 'EXECUTE PROCEDURE "dba".sp_rep_extens_cursada_x_carrera()';
		$result = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $result;        
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = ci_ext_alumnos_x_carrera::get_alu_x_carrera_solicitaron_ext();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- formulario_procesar ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar($datos)
	{
		$db = MisConsultas::getConexion();
		$anio_actual = date('Y'); 
		$sql = "EXECUTE PROCEDURE sp_rep_alumnos_regulares($anio_actual)";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha;    
	}
	
	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_alumnos_regulares'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
	}
}

?>