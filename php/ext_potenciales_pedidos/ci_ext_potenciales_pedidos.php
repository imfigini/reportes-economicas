<?php
require_once('MisConsultas.php');

class ci_ext_potenciales_pedidos extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	function get_periodos_lectivos()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT DISTINCT periodo_lectivo FROM sga_periodos_lect WHERE periodo_lectivo LIKE '%cuatrimestre'";
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__filtro(toba_ei_formulario $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function get_potenciales_extensiones($filtro)
	{
		$db = MisConsultas::getConexion();
		$periodo_lectivo = $filtro['CUATRIMESTRE'];
		$sql = "EXECUTE PROCEDURE 'dba'.sp_rep_potenciales_pedidos_extension('$periodo_lectivo')";
		$result = $db->query($sql)->fetchAll(); 	//(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$datos = $this->get_potenciales_extensiones($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

}

?>