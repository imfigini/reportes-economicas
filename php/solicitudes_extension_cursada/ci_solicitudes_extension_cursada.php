<?php
require_once ('MisConsultas.php');
require_once ('parametros.php');

class ci_solicitudes_extension_cursada extends toba_ci
{
	protected $s__datos_filtro;


	function get_solicitudes_extension($estado)
	{
		$sql = "SELECT 	E.nro_transaccion, 
						E.legajo, 
						P.apellido || ', ' || P.nombres AS alumno,
						E.carrera AS cod_carrera, 
						C.nombre || ' (' || C.carrera || ')' AS carrera, 
						E.plan,
						E.materia AS cod_materia,
						M.nombre || ' (' || M.materia || ')' AS materia,
						E.acta,
						E.f_venc_reg_ant,
						E.fecha_pedido_extension,
						U.descripcion AS motivo,
						E.observaciones,
						E.empresa,
						E.hs_trabajadas,
						E.procesada
					FROM uexa_prorrogas_pedidas E
						JOIN sga_alumnos A ON (A.unidad_academica = E.unidad_academica AND A.legajo = E.legajo AND A.carrera = E.carrera)
						JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
						JOIN sga_carreras C ON (C.carrera = E.carrera)
						JOIN sga_materias M ON (M.materia = E.materia)
						JOIN uexa_mot_prorroga U ON (U.motivo = E.motivo)";
				
		if ($estado == 'N' OR $estado == 'S')
			$sql .= " WHERE E.procesada = '$estado'";
		
		$sql .= " ORDER BY E.f_venc_reg_ant";
		
		$db = MisConsultas::getConexion();
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;		
	}
	
	function calcular_cantidad_pedidos_anteriores($datos)
	{
		$result = Array();
		foreach ($datos AS $dato)
		{
			
			$cod_materia = $dato['COD_MATERIA'];
			$legajo = $dato['LEGAJO'];
			$cod_carrera = $dato['COD_CARRERA'];
			
			$sql = "SELECT COUNT(f_prorroga_hasta) AS cant
						FROM sga_prorrogas_regu
						WHERE legajo = '$legajo'
							AND carrera = $cod_carrera
							AND materia = '$cod_materia'";
			
			$db = MisConsultas::getConexion();
			$cant = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			$dato['cant_prorrogas_otorgadas'] = $cant[0]['CANT'];
			$result[] = $dato;
		}
		return $result;
	}
	
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
			$estado = $this->s__datos_filtro['procesadas'];
			$datos = $this->get_solicitudes_extension($estado);
		} 
		else 
		{
			$datos = $this->get_solicitudes_extension('N');
		}
		$datos = $this->calcular_cantidad_pedidos_anteriores($datos);
		$cuadro->set_datos($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__cuadro__seleccion($seleccion)
	{
		$nro_transaccion = $seleccion['NRO_TRANSACCION'];
		$sql = "SELECT archivo FROM uexa_prorrogas_pedidas WHERE nro_transaccion = $nro_transaccion";
		$db = MisConsultas::getConexion();
		$archivo = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		ei_arbol($archivo, 'archivo');
		$directorio = PDF_PATH_EXTENSIONES;		
		ei_arbol($directorio, 'directorio');
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
		// Indico cual va a ser el cuadro de donde se obtienen las claves, para mostrar_pdf
		toba::memoria()->set_dato('cuadro', CUADRO_DOCUMENTACION_EXTENSIONES);

	}

}
?>