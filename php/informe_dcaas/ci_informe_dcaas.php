<?php
class ci_informe_dcaas extends toba_ci
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
		$fuente = $this->dep('datos')->tabla('prueba');
		if (isset($this->s__datos_filtro)) {
			$datos = $fuente->get_informacion_DCAAS($this->s__datos_filtro);
		} else {
			$datos = $fuente->get_informacion_DCAAS();
		}
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__eliminar($datos)
	{
		$this->dep('datos')->resetear();
		$this->dep('datos')->cargar($datos);
		$this->dep('datos')->eliminar_todo();
		$this->dep('datos')->resetear();
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('prueba')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('prueba')->set($datos);
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
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