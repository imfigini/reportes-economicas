<?php
require_once ('MisConsultas.php');

class ci_alumnos_proximos_a_recibirse extends toba_ci
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

	static function get_alumnos_con_cant_materias_le_faltan($cant_materias)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT * FROM rep_cant_materias_faltan ";
		if (trim($cant_materias) != "")
		{
			$sql = $sql . " WHERE cant_materias_faltan <= $cant_materias " ;
		}
		$sql = $sql . " ORDER BY alumno";
		//ei_arbol($sql);
		$alumnos = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
		return $alumnos;
	}

		
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			//ei_arbol($filtro);
			$cant_materias = $filtro['cant_materias'];
			$datos = $this->get_alumnos_con_cant_materias_le_faltan($cant_materias);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
			//$cuadro->set_datos($this->dep('datos')->tabla('prueba')->get_listado());
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

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
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE dba.sp_rep_cant_materias_faltan()";
                //ei_arbol($sql);
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];      
	}

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_cant_materias_faltan'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

}