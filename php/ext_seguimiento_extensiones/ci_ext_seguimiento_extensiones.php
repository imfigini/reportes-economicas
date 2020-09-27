<?php
require_once('MisConsultas.php');

class ci_ext_seguimiento_extensiones extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function get_seguimientos_extension()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE 'dba'.sp_extensiones_seguimiento()";
		$result = $db->query($sql)->fetchAll(); 	//(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_seguimientos_extension();
		$cuadro->set_datos($datos);
	}

}

?>
