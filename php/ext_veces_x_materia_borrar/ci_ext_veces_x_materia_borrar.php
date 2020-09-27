<?php
require_once('MisConsultas.php');

class ci_ext_veces_x_materia extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	//Número de veces que se solicita extensión para cada asignatura de plan
	function get_veces_x_materia()
	{
		$db = MisConsultas::getConexion();
		$sql = 'EXECUTE PROCEDURE "dba".sp_rep_ext_veces_x_materia()';
		$result = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_veces_x_materia();
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
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(Reportes_ei_filtro $filtro)
	{
	}

}
?>