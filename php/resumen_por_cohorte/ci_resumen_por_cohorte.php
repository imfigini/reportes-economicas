<?php
require_once 'consultas_extension.php';

class ci_resumen_por_cohorte extends toba_ci
{
	protected $s__datos_filtro;
	protected $alumnos_cohorte;

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

	
	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) 
		{
			$form->set_datos($this->dep('datos')->tabla('prueba')->get());
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('prueba')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	//------------ Calculo de datos --------------------------------------
	
	//Retrona la cantidad de alumnos ingresantes en una determinada cohorte y una determinada carrera
	function get_cantidad_ingresantes_cohorte()
	{
		$filtro = $this->s__datos_filtro;
		if (isset($filtro))
		{
			ei_arbol($filtro, 'filtro');
			$alumnos_cohorte = consultas_extension::get_alumnos_ingresantes_cohorte($filtro);
			return count($alumnos_cohorte);
		}
		else
			return 0;

	}
}

?>