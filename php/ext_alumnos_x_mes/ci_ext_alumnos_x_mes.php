<?php
require_once('MisConsultas.php');

class ci_ext_alumnos_x_mes extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	//Número de alumnos que pidieron extensiones por mes y por año
	function get_alu_x_mes_solicitaron_ext()
	{
		$db = MisConsultas::getConexion();
		$sql = 'EXECUTE PROCEDURE "dba".sp_rep_extens_cursada_x_mes()';
		$result = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_alu_x_mes_solicitaron_ext();
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

}

?>