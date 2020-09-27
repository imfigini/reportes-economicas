<?php
require_once('consultas_extension.php');

class ci_alumnos_sin_requisitos_pps extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = consultas_extension::get_alumnos_sin_requisitos_para_cursar_PPS();
		$cuadro->set_datos($datos);
	}

}

?>