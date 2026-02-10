const url =
import.meta.env.MODE === "production"

? import.meta.env.VITE_API_URL_PROD
// URL de desarrollo si la condición de producción no se cumple
: import.meta.env.VITE_API_URL_DEV;
// URL de producción si la condición de desarrollo se cumple
export default url;

