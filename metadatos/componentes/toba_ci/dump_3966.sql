------------------------------------------------------------
--[3966]--  Informe ingresantes - CI 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'Reportes', --proyecto
	'3966', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'14', --punto_montaje
	'ci_informe_ingresantes', --subclase
	'informe_ingresantes/ci_informe_ingresantes.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Informe ingresantes - CI', --nombre
	NULL, --titulo
	'0', --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2016-05-09 09:36:45', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'Reportes', --objeto_mt_me_proyecto
	'3966', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	'100%', --ancho
	'300px', --alto
	NULL, --posicion_botonera
	NULL, --tipo_navegacion
	'0', --botonera_barra_item
	'0', --con_toc
	NULL, --incremental
	NULL, --debug_eventos
	NULL, --activacion_procesar
	NULL, --activacion_cancelar
	NULL, --ev_procesar
	NULL, --ev_cancelar
	NULL, --objetos
	NULL, --post_procesar
	NULL, --metodo_despachador
	NULL  --metodo_opciones
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2655', --dep_id
	'3966', --objeto_consumidor
	'3968', --objeto_proveedor
	'cuadro_ingres_carrera_col', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2654', --dep_id
	'3966', --objeto_consumidor
	'3967', --objeto_proveedor
	'cuadro_ingres_carrera_loc', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3480', --dep_id
	'3966', --objeto_consumidor
	'4876', --objeto_proveedor
	'cuadro_ingres_col', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3481', --dep_id
	'3966', --objeto_consumidor
	'4877', --objeto_proveedor
	'cuadro_ingres_loc', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2651', --dep_id
	'3966', --objeto_consumidor
	'3963', --objeto_proveedor
	'cuadro_ingresantes_carrera', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2650', --dep_id
	'3966', --objeto_consumidor
	'3962', --objeto_proveedor
	'datos', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2653', --dep_id
	'3966', --objeto_consumidor
	'3965', --objeto_proveedor
	'filtro', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2652', --dep_id
	'3966', --objeto_consumidor
	'3964', --objeto_proveedor
	'formulario', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_ci_pantalla
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'Reportes', --objeto_ci_proyecto
	'3966', --objeto_ci
	'1722', --pantalla
	'pant_edicion', --identificador
	'1', --orden
	'Pantalla', --etiqueta
	'Resumen de cantidad de ingresantes (que aprobaron el curso de ingreso). La consulta se hace sobre guarani de producción. 
Informe ingresantes - Sólo se consideran aquellos alumnos nuevos, que no tienen otorgada ninguna equivalencia, ya sea por cambio de carrera, o de facultad o de universidad. Son los que empiezan de cero la totalidad de la carrera a la cual se inscriben. Ya sea hayan aprobado el curso de ingreso en esta instancia, o lo tengan aprobado con anterioridad para otra carrera. No se consideran los alumnos de la LEM.
- Total
- Por carrera
- Por carrera y localidad de colegio secundario que egresaron
- Por localidad de colegio secundario que egresaron (sin importar carrera)
- Por carrera y colegio de Tandil
- Por colegio de Tandil (sin importar carrera)', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'14'  --punto_montaje
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objetos_pantalla
------------------------------------------------------------
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'2', --orden
	'2651'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'1', --orden
	'2652'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'0', --orden
	'2653'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'3', --orden
	'2654'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'5', --orden
	'2655'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'6', --orden
	'3480'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1722', --pantalla
	'3966', --objeto_ci
	'4', --orden
	'3481'  --dep_id
);
