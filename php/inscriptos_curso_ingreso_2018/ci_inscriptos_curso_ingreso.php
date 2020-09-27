<?php
require_once('consultas_extension.php');
require_once('MisConsultas.php');

class ci_inscriptos_curso_ingreso extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

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

	//VER: http://www.exa.unicen.edu.ar/reportes-guarani-grado/curso-ingreso/inscriptos.php?reporte=inscriptos
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{	
		if (isset($this->s__datos_filtro)) 
		{	
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$datos = consultas_extension::get_datos_inscriptos_ingreso_2018($anio);
			if (count($datos) > 0) {
				$cuadro->set_datos($datos);
			} 
		}  
		else 
			$cuadro->limpiar_columnas();
	}


	//-----------------------------------------------------------------------------------
    //---- procesar ---------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function get_ultima_actualizacion()
    {
        $db = MisConsultas::getConexionMini();
        $sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
                    FROM rep_fecha_actualiz_tablas
                        WHERE tabla = 'rep_datos_inscriptos'";
        $fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];        
    }

    function evt__procesar__procesar()
    {
		$db = MisConsultas::getConexionMini();
        $sql = 'SELECT MAX(anio_academico) FROM sga_anio_academico';
		$anio = $db->query($sql)->fetchAll(PDO::FETCH_NUM);
		$curYear = $anio[0][0];

        $sql = "EXECUTE PROCEDURE sp_getDatosInscriptos($curYear)";
		$db->query($sql)->fetchAll();
    }        

}

?>