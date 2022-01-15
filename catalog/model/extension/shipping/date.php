<?php
class ModelExtensionShippingDate extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/date');

		$method_data = array();

        $quote_data = array();

        $lastDays = $this->getLastFourDay();

        foreach( $lastDays as $day )
        {
            $quote_data['date_'.$day["date"]] = array(
                'code'         => 'date.date_'.$day["date"],
                'title'        => $this->language->get('text_description') . ' ' . $day["day"],
                'cost'         => 5.00,
                'tax_class_id' => 0,
                'text'         => $this->currency->format(5.00, $this->session->data['currency'])
            );
        }

        $method_data = array(
            'code'       => 'date',
            'title'      => $this->language->get('text_title'),
            'quote'      => $quote_data,
            'sort_order' => $this->config->get('shipping_date_sort_order'),
            'error'      => false
        );
	

		return $method_data;
	}

    public function getLastFourDay()
    {
        $lastDays = [];

        for( $i=1; $i <= 5; $i++ )
        {
            $lastDays[] = [
                'day' => date("D", time() + 86400 * $i ),
                'date' => date("Y-m-d", time() + 86400 * $i )
            ];
                
        }

        return $lastDays;

    }

}
