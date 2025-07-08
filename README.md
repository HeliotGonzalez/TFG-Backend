## Modelos de la Aplicación

A continuación se ofrece una visión general de los modelos Eloquent más relevantes utilizados en este proyecto:

- **Amigo** – gestiona las relaciones de amistad entre usuarios.  
- **Chat** – representa los mensajes privados intercambiados entre usuarios.  
- **Comentario** – comentarios de usuarios que pueden incluir respuestas a otros comentarios.  
- **DailyChallenge** – almacena las palabras y vídeos seleccionados para la funcionalidad de reto diario.  
- **Diccionario** – entradas del diccionario que vinculan un usuario con vídeos guardados.  
- **Etiqueta** – modelo sencillo de etiqueta asociado a significados. Es una temática o un hastag.  
- **Palabra** – palabras individuales vinculadas a un significado.  
- **Reporte** – denuncias de usuarios presentadas contra vídeos.  
- **Role** – modelo mínimo que describe los roles de usuario disponibles.  
- **Significado** – significados de palabras con relaciones a etiquetas, palabras y vídeos.  
- **Suggestion** – sugerencias enviadas por los usuarios para mejoras de la aplicación.  
- **User** – usuarios de la aplicación; maneja autenticación y relaciones.  
- **UserVideo** – modelo intermedio que almacena los “me gusta” o “no me gusta” de un usuario sobre un vídeo.  
- **Video** – vídeos de lengua de signos subidos y metadatos relacionados.  
- **significadoEtiqueta** – tabla intermedia que une significados y etiquetas.  
- **significado_propuesto** – actualizaciones propuestas por usuarios a un significado de palabra.  

## Controladores

Los controladores HTTP exponen los principales endpoints de la API y la lógica de negocio:

- **AmigoController** – gestiona el envío y la aceptación de solicitudes de amistad.  
- **ChatController** – administra conversaciones de chat y entrega de mensajes.  
- **DailyChallengeController** – valida y registra los resultados del reto diario.  
- **DiccionarioController** – añade o elimina vídeos del diccionario de un usuario y proporciona datos para los retos.  
- **EtiquetaController** – devuelve las etiquetas disponibles.  
- **PalabraController** – operaciones CRUD para palabras y sus significados asociados.  
- **ReporteController** – lista y actualiza las denuncias sobre vídeos.   
- **SignificadoPropuestoController** – procesa los significados nuevos propuestos por los usuarios.  
- **SuggestionController** – recibe y modera las sugerencias de funcionalidades.  
- **UserController** – registro de usuarios, autenticación y gestión de perfil.   
- **VideoController** – subida, valoración y gestión de vídeos.  
