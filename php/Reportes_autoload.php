<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class Reportes_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'Reportes_comando' => 'extension_toba/Reportes_comando.php',
		'Reportes_modelo' => 'extension_toba/Reportes_modelo.php',
		'Reportes_ci' => 'extension_toba/componentes/Reportes_ci.php',
		'Reportes_cn' => 'extension_toba/componentes/Reportes_cn.php',
		'Reportes_datos_relacion' => 'extension_toba/componentes/Reportes_datos_relacion.php',
		'Reportes_datos_tabla' => 'extension_toba/componentes/Reportes_datos_tabla.php',
		'Reportes_ei_arbol' => 'extension_toba/componentes/Reportes_ei_arbol.php',
		'Reportes_ei_archivos' => 'extension_toba/componentes/Reportes_ei_archivos.php',
		'Reportes_ei_calendario' => 'extension_toba/componentes/Reportes_ei_calendario.php',
		'Reportes_ei_codigo' => 'extension_toba/componentes/Reportes_ei_codigo.php',
		'Reportes_ei_cuadro' => 'extension_toba/componentes/Reportes_ei_cuadro.php',
		'Reportes_ei_esquema' => 'extension_toba/componentes/Reportes_ei_esquema.php',
		'Reportes_ei_filtro' => 'extension_toba/componentes/Reportes_ei_filtro.php',
		'Reportes_ei_firma' => 'extension_toba/componentes/Reportes_ei_firma.php',
		'Reportes_ei_formulario' => 'extension_toba/componentes/Reportes_ei_formulario.php',
		'Reportes_ei_formulario_ml' => 'extension_toba/componentes/Reportes_ei_formulario_ml.php',
		'Reportes_ei_grafico' => 'extension_toba/componentes/Reportes_ei_grafico.php',
		'Reportes_ei_mapa' => 'extension_toba/componentes/Reportes_ei_mapa.php',
		'Reportes_servicio_web' => 'extension_toba/componentes/Reportes_servicio_web.php',
	);
}
?>