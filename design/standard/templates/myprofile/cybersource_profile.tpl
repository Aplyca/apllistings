{def $card_type = fetch( 'content', 'object', hash( 'object_id', $csp.data_map.cc_type_id.content.id ) )}

{attribute_view_gui attribute=$card_type.data_map.image}<br/>

<strong>{$csp.data_map.billingfirstname.contentclass_attribute_name}:</strong> {attribute_view_gui attribute=$csp.data_map.billingfirstname}<br/>
<strong>{$csp.data_map.billinglastname.contentclass_attribute_name}:</strong> {attribute_view_gui attribute=$csp.data_map.billinglastname}<br/>
<strong>{"Credit Card"|i18n("design/content/billing")}:</strong> **********{attribute_view_gui attribute=$csp.data_map.cc_last_four_digits}<br/>
<strong>{$csp.data_map.expiremonth.contentclass_attribute_name}:</strong> {attribute_view_gui attribute=$csp.data_map.expiremonth}<br/>
<strong>{$csp.data_map.expireyear.contentclass_attribute_name}:</strong> {attribute_view_gui attribute=$csp.data_map.expireyear}<br/>
