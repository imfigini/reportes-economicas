<?php
class ci_hist_acad extends Reportes_ci
{
	//-----------------------------------------------------------------------------------
	//---- cuadro_finales ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_finales(Reportes_ei_cuadro $cuadro)
	{
            $datos = $this->cn()->get_hist_academica();
            $cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_cursadas --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_cursadas(Reportes_ei_cuadro $cuadro)
	{
            $datos = $this->cn()->get_cursadas();
            $cuadro->set_datos($datos);

        }

	//-----------------------------------------------------------------------------------
	//---- cuadro_info_carreras ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_info_carreras(Reportes_ei_cuadro $cuadro)
	{
            $datos = $this->cn()->get_carreras();
            $cuadro->set_datos($datos);            
	}

}
?>