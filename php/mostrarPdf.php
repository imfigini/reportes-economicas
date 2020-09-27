<?php

	error_reporting(E_ALL); 
    ini_set('display_errors', 1);
	   
	require_once ('parametros.php');
	require_once ('MisConsultas.php');
	require_once (PDF_PATH.'/class.ezpdf.php');
	
	function get_nombre_archivo_pdf($clave)
	{
		$sql = "SELECT archivo FROM uexa_prorrogas_pedidas WHERE nro_transaccion = $clave";
		$db = MisConsultas::getConexion();
		$archivo = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $archivo;
	}

	function recuperar_clave_fila()
	{
		$cuadro = toba::memoria()->get_dato("cuadro");
		$clave_get = toba::memoria()->get_parametro('fila_safe');
		$claves_originales = toba_ei_cuadro::recuperar_clave_fila($cuadro, $clave_get);
		$nro_transaccion = $claves_originales['NRO_TRANSACCION'];
		return $nro_transaccion;
	}
	
	function mostrarPdf($nombreArchivo)
	{
		header('Content-type: application/pdf');
		$contenidoArchivo = readfile($nombreArchivo);
		echo ($contenidoArchivo);
	}

	$nro_transaccion = recuperar_clave_fila();
	$nombreDirectorio = PDF_PATH_EXTENSIONES;		
	$nombreArchivo = get_nombre_archivo_pdf($nro_transaccion);
	$nombreCompleto = $nombreDirectorio.'/'.$nombreArchivo[0]['ARCHIVO'];
	mostrarPdf($nombreCompleto);
	
?>