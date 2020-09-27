<?php
require_once('MisConsultas.php');

class ci_informe_insc_cursadas extends toba_ci
{
	protected $s__datos_filtro;
	protected $s__carreras = " ('206', '213', '212') ";  //Ing. Sistemas, TUDAI, TUPAR

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
			$datos = $this->get_datos($filtro);
			$cuadro->set_datos($datos);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	

	//---- Funciones -------------------------------------------------------------------

	/** Retorna el listado de materias de los planes de las carreras de sistemas **/
	function get_carreras() 
	{
		$db = MisConsultas::getConexion();
		$sqlText = "SELECT carrera, nombre || ' (' || carrera || ')' AS carrera_nombre 
					 FROM sga_carreras 
						 WHERE carrera IN $this->s__carreras  
					 ORDER BY nombre ";
		$carreras = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
		return $carreras;
	}

	/** Retorna el listado de años de cursada de la carrera */
	function get_anio_cursada($carrera = null)
	{
		if (!isset($carrera)) {
			$carrera = $this->s__carreras;
		} else	{
			$carrera = ' ( '.$carrera.') ';
		}
		$db = MisConsultas::getConexion();
		$sql = "SELECT DISTINCT NVL(anio_de_cursada, 0) AS anio_cursada, 
								NVL(anio_de_cursada, 'Sin especificar') AS anio_descripcion
					FROM sga_atrib_mat_plan
					WHERE carrera IN $carrera
					ORDER BY 2 ASC ";
		return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_datos($filtro)
	{
		$carrera = $filtro['CARRERA_NOMBRE'];
		$anio_academico = $filtro['ANIO_ACADEMICO'];
		$anio_cursada = $filtro['ANIO_CURSADA'];
		$db = MisConsultas::getConexion();

		$sql = "SELECT DISTINCT P.materia, 
								P.nombre_materia || ' (' || P.materia || ')' AS materia_nombre, 
								P.anio_de_cursada, 
								C.comision, 
								C.periodo_lectivo
				FROM sga_atrib_mat_plan P, sga_comisiones C
					WHERE C.materia = P.materia
					AND C.anio_academico = $anio_academico
					AND P.carrera = '$carrera' ";
		if (isset ($anio_cursada)) {
			if ($anio_cursada == 0) {
				$sql .= " AND P.anio_de_cursada IS NULL ";
			} else {
				$sql .= " AND P.anio_de_cursada = $anio_cursada ";
			}
		}
		$sql .= " ORDER BY 3, 2, 5 ";

		$comisiones = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

		$resultado = Array();
		foreach($comisiones as $comision)
		{
			$comision['CANT_TOTAL'] = $this->get_inscriptos_total($db, $carrera, $comision['COMISION']);
			$comision['CANT_PRIMERA_VEZ'] = $this->get_inscriptos_primera_vez($db, $carrera, $comision['MATERIA'], $comision['COMISION']);
			$comision['CANT_RECURSANTES'] = $this->get_inscriptos_recursantes($db, $carrera, $comision['MATERIA'], $comision['COMISION']);
			$resultado[] = $comision;
		}
		return $resultado;
	}

	function get_inscriptos_total($db, $carrera, $comision)
	{
		$sql = "SELECT COUNT(legajo) AS cant
				FROM sga_insc_cursadas I
					WHERE I.carrera = '$carrera'
					AND I.comision = $comision ";
		$cant = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cant[0]['CANT'];
	}

	function get_inscriptos_primera_vez($db, $carrera, $materia, $comision)
	{
		$sql = "SELECT COUNT(legajo) AS cant
				FROM sga_insc_cursadas I
					WHERE I.carrera = '$carrera'
					AND I.comision = $comision
					AND I.fecha_inscripcion = ( (SELECT MIN(X.fecha_inscripcion) 
							FROM sga_insc_cursadas X
							WHERE I.unidad_academica = X.unidad_academica
							AND I.carrera = X.carrera
							AND I.legajo = X.legajo
							AND X.comision IN (SELECT comision FROM sga_comisiones WHERE materia = '$materia') ) ) ";

		$cant = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cant[0]['CANT'];
	}

	function get_inscriptos_recursantes($db, $carrera, $materia, $comision)
	{
		$sql = "SELECT COUNT(legajo) AS cant
				FROM sga_insc_cursadas I
					WHERE I.carrera = '$carrera'
					AND I.comision = $comision
					AND I.fecha_inscripcion > ( (SELECT MIN(X.fecha_inscripcion) 
							FROM sga_insc_cursadas X
							WHERE I.unidad_academica = X.unidad_academica
							AND I.carrera = X.carrera
							AND I.legajo = X.legajo
							AND X.comision IN (SELECT comision FROM sga_comisiones WHERE materia = '$materia') ) ) ";

		$cant = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cant[0]['CANT'];
	}


}

?>