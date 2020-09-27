<?php
require_once('MisConsultas.php');

class ci_detalle_cursadas_sistemas extends toba_ci
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
			$anio = $filtro['ANIO_CURSADA'];
			$carrera = $filtro['CARRERA_NOMBRE'];
			$datos = $this->get_datos($anio, $carrera);
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

   function get_datos($anio, $carrera)
   {
		$db = MisConsultas::getConexion();
		$sql = "SELECT DISTINCT S.sede, 
								S.nombre AS sede_nombre, 
								M.materia, 
								M.nombre AS materia_nombre, 
								COUNT(D.legajo) AS inscriptos
				FROM sga_actas_cursado AC, sga_det_acta_curs D, sga_comisiones C, sga_materias M, sga_sedes S
					WHERE AC.acta = D.acta
					AND D.carrera = '$carrera'
					AND AC.comision = C.comision
					AND C.anio_academico = $anio
					AND C.sede = S.sede
					AND C.materia = M.materia
				GROUP BY 1,2,3,4
				HAVING COUNT(D.legajo) > 0";

		$datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
//		ei_arbol($datos);
		$cant = count($datos);
		for ($i=0; $i<$cant; $i++)
		{
			$materia = $datos[$i]['MATERIA'];
			$sede = $datos[$i]['SEDE'];
			$datos[$i]['APROBADOS'] = $this->get_cantidad($db, $anio, $materia, $carrera, $sede, 'A') ;
			$datos[$i]['PROMOCIONADOS'] = $this->get_cantidad($db, $anio, $materia, $carrera, $sede, 'P') ;
			$datos[$i]['REPROBADOS'] = $this->get_cantidad($db, $anio, $materia, $carrera, $sede, 'R') ;
			$datos[$i]['AUSENTES'] = $this->get_cantidad($db, $anio, $materia, $carrera, $sede, 'U') ;
		}
		return $datos;
   }

   function get_cantidad($db, $anio, $materia, $carrera, $sede, $calidad) 
   {
		$sql = "SELECT COUNT(legajo) AS cantidad
				FROM sga_actas_cursado AC, sga_det_acta_curs D, sga_comisiones C
					WHERE AC.acta = D.acta
					AND AC.comision = C.comision
					AND C.anio_academico = $anio
					AND C.materia = '$materia'
					AND D.resultado = '$calidad'
					AND C.sede = '$sede'
					AND D.carrera = $carrera ";
		$datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $datos[0]['CANTIDAD'];
   }
}

?>