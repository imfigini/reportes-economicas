<?php
require_once('MisConsultas.php');

class ci_requisito1 extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__lista_materias;


	//---- Filtro -----------------------------------------------------------------------

	/** Filtrar arreglo. Dado un arreglo, compuesto por ('MATREIA','NOMBRE'), eliminar la materia indicada en $valor. **/
	function filtrar_materias($arreglo, $valor)
	{
		$max = count($arreglo);
		for ($i= 0; $i<$max; $i++)
		{
			if (isset($arreglo[$i]) && $arreglo[$i]['MATERIA'] == $valor)
				unset($arreglo[$i]);
		}
		return $arreglo;
	}
	
	/** Retorna el listado de materias pertenecientes a algún plan activo vigente **/
	function get_materias() 
	{
		$db = MisConsultas::getConexion ();
		
		$sqlText = "SELECT DISTINCT M.materia, M.nombre || ' (' || M.materia || ')' AS nombre
						FROM sga_materias M, sga_atrib_mat_plan A, sga_planes P
						WHERE M.unidad_academica = A.unidad_academica AND M.materia = A.materia
							AND A.unidad_academica = P.unidad_academica AND A.carrera = P.carrera AND A.plan = P.plan AND A.version = P.version_actual
							AND P.estado = 'V' --Activo Vigente
						ORDER BY nombre";

		$materias = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
		
		//Descarto las materias ya seleccionadas en $s__lista_materias
		if (isset ($this->s__lista_materias))
			foreach ($this->s__lista_materias AS $m)
				$materias = $this->filtrar_materias($materias, $m);
		
		return $materias;
	}	
		
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
		$this->s__lista_materias[] = $datos['NOMBRE'];
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
		unset($this->s__lista_materias);
	}

	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$db = MisConsultas::getConexion ();
			$materias = $this->s__lista_materias;
			$datos = array();
			foreach ($materias AS $m)
			{
				$sql = "SELECT DISTINCT M.materia, M.nombre
							FROM sga_materias M
							WHERE M.materia = '$m'";
				
				$materia = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);			
				$datos[] = $materia[0];
			}
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

	
	
	function get_alumnos_con_final_aprobado($filtro)
	{
		$materia = $filtro['NOMBRE'];
		
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT V.legajo, P.apellido || ', ' || P.nombres AS alumno, D.e_mail
					FROM vw_hist_academica V, sga_alumnos A, sga_personas P, vw_datos_censales_actuales D
						WHERE 	V.materia = '$materia'
							AND V.resultado = 'A'
							AND V.unidad_academica = A.unidad_academica
							AND V.carrera = A.carrera 
							AND V.legajo = A.legajo
							AND A.regular = 'S' AND A.calidad = 'A'
							AND A.legajo NOT IN (SELECT legajo FROM sga_alumnos WHERE calidad = 'E')
							AND A.unidad_academica = P.unidad_academica
							AND A.nro_inscripcion = P.nro_inscripcion 
							AND P.unidad_academica = D.unidad_academica 
							AND P.nro_inscripcion = D.nro_inscripcion";
							
		
		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		
		$max = count($alumnos);
		for ($i=0; $i<$max; $i++)
		{
			$num_aleatorio = rand(1,5000);
			$alumnos[$i]['NUM_ALEAT'] = $num_aleatorio;
		}
		
		return $alumnos;
	}
}

?>