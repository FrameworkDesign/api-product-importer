import MapCollectionFieldsPanel from "./components/Mapping/MapCollectionFieldsPanel";
import ApiProductDataImporterFieldType from "./components/FieldTypes/ApiProductDataImporterFieldType";
import PushApiProductIntoStatamicHelper from "./components/Import/PushApiProductIntoStatamicHelper";



Statamic.$components.register("api_product_data_importer-fieldtype", ApiProductDataImporterFieldType);
Statamic.$components.register("map-collection-fields-panel", MapCollectionFieldsPanel);
Statamic.$components.register("push-api-product-into-statamic-helper", PushApiProductIntoStatamicHelper);



