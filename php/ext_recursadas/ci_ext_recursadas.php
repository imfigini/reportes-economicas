<?php
require_once('MisConsultas.php');

class ci_ext_recursadas extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function get_recursadas()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE 'dba'.sp_rep_extensiones_recursadas()";
		$result = $db->query($sql)->fetchAll(); 	//(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{	
		$datos = $this->get_recursadas();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

}

?>