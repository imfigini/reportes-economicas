<?php
require_once('MisConsultas.php');

class ci_pedidos_de_extensiones extends toba_ci
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

	//Retorna el listado detallado de alumnos que han solicitado extensiones
	function get_pedidos_extensiones($filtro)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT C.carrera, M.nombre AS materia_nombre, P.legajo, A.apellido || ', ' || A.nombres AS alumno, P.f_venc_reg_ant, P.f_prorroga_hasta, P.fecha_alta
					FROM sga_carreras C, sga_materias M, sga_prorrogas_regu P, sga_personas A
					WHERE P.unidad_academica = C.unidad_Academica AND P.carrera = C.carrera
						AND P.unidad_academica = M.unidad_academica AND P.materia = M.materia
						AND P.legajo = A.nro_inscripcion ";
		if (isset($filtro))
		{	
			$filtro = $filtro['descripcion'];
			$condicion = "AND (	C.carrera = '$filtro'
								OR lower(M.nombre) LIKE lower('%$filtro%')
								OR P.legajo LIKE '$filtro'	
								OR lower(A.apellido) LIKE lower('%$filtro%')
								OR lower(A.nombres) LIKE  lower('%$filtro%') )";
			$sql .= $condicion;
		}
		$sql .= 'ORDER BY alumno';
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$datos = $this->get_pedidos_extensiones($this->s__datos_filtro);
			$cuadro->set_datos($datos);		
		} 
		else 
		{
			$datos = $this->get_pedidos_extensiones(NULL);
			$cuadro->set_datos($datos);		
			//$cuadro->set_datos($this->dep('datos')->tabla('prueba')->get_listado());
		}
	}

}

?>