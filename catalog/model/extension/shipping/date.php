<?php
class ModelExtensionShippingDate extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/date');
 
		$method_data = array();

        $quote_data = array();

        $lastDays = $this->getLastFourDay();

        $totals = $this->getTotal();
        $totals = $totals[0]['text'];
        $totals = explode("£",$totals);
        if( $totals[1] >= 50 ){
            foreach( $lastDays as $day )
            {
                $quote_data['date_'.$day["date"]] = array(
                    'code'         => 'date.date_'.$day["date"],
                    'title'        => $this->language->get('text_description') . ' ' . $day["day"],
                    'cost'         => 0,
                    'tax_class_id' => 0,
                    'text'         => $this->currency->format(0, $this->session->data['currency'])
                );
            }
        }else{
            foreach( $lastDays as $day )
            {
                if( $day["day"] != "Sun" )
                {
                    $quote_data['date_'.$day["date"]] = array(
                        'code'         => 'date.date_'.$day["date"],
                        'title'        => $this->language->get('text_description') . ' ' . $day["day"],
                        'cost'         => $this->config->get('shipping_date_delivery_price'),
                        'tax_class_id' => 0,
                        'text'         => $this->currency->format($this->config->get('shipping_date_delivery_price'), $this->session->data['currency'])
                    );
                }
            }
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

    private function getLastFourDay()
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

    private function getTotal()
    {
        			// Totals
			$this->load->model('setting/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;
			
			// Because __call can not keep var references so we put them into an array. 			
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);
			
			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->model_setting_extension->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get('total_' . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);
						
						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

            $data['totals'] = array();

			foreach ($totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $this->session->data['currency'])
				);
			}

            return $data['totals'];
    }

}
