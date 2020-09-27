<?php
require_once('MisConsultas.php');

class ci_ficha_de_alumno extends toba_ci
{
	protected $s__datos_filtro;

	//---- Filtro -----------------------------------------------------------------------

	function conf__filtro(toba_ei_formulario $filtro)
	{
            if (isset($this->s__datos_filtro)) 
            {
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
                $filtro['departamento'] = 'D004';				
                $filtro['carrera'] = '211';
                $filtro['regular_activo'] = true;
                $datos = MisConsultas::getAlumnos($filtro);				
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
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
            $this->cn()->cargar($datos);
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
            $this->cn()->resetear();
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

	//-----------------------------------------------------------------------------------
	//---- form_datos_comunes -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form_datos_comunes(Reportes_ei_formulario $form)
	{
            $clave = $this->cn()->get_clave_actual();
            $form->set_datos($clave);
	}

}
?>