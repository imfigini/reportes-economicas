<?php
require_once('MisConsultas.php');

class ci_ext_detalle_alumnos extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	//Retorna el listado detallado de alumnos que han solicitado extensiones
	function get_detalle_extensiones()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT C.nombre AS carrera_nombre, M.nombre_reducido AS materia_nombre, R.legajo, R.f_venc_reg_ant, R.f_prorroga_hasta, R.fecha_alta, R.resultado_final, R.fecha_final 
					FROM rep_extensiones_cursada R, sga_carreras C, sga_materias M
						WHERE R.carrera = C.carrera
							AND R.materia = M.materia
					ORDER BY R.fecha_alta";
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = ci_ext_detalle_alumnos::get_detalle_extensiones();
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
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
	//---- extensiones_procesar ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__extensiones_procesar__procesar($datos)
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE sp_rep_extensiones_cursada()";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha;	
	}

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_extensiones_cursada'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}
}
?>