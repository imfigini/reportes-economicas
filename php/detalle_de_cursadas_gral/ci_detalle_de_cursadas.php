<?php
require_once('consultasCursadas.php');

class ci_detalle_de_cursadas extends toba_ci
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

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__datos_filtro)) 
            {
                $filtro = $this->s__datos_filtro;
                $carrera = $filtro['CARRERA'];
                $materia = $filtro['MATERIA'];
                $anio_cursada = $filtro['ANIO_CURSADA'];
				$condicion = $filtro['CONDICION'];
				$sede = $filtro['SEDE'];
                $datos = ConsultasCursadas::get_alumnos_cursada($materia, $anio_cursada, $carrera, $condicion, $sede);
                //ei_arbol($datos, 'datos'); 
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
	}

}

?>