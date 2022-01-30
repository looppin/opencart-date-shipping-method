<?php
class ControllerExtensionShippingDate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/date');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_date', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/date', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/date', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		if (isset($this->request->post['shipping_date_geo_zone_id'])) {
			$data['shipping_date_geo_zone_id'] = $this->request->post['shipping_date_geo_zone_id'];
		} else {
			$data['shipping_date_geo_zone_id'] = $this->config->get('shipping_date_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_date_status'])) {
			$data['shipping_date_status'] = $this->request->post['shipping_date_status'];
		} else {
			$data['shipping_date_status'] = $this->config->get('shipping_date_status');
		}

		if (isset($this->request->post['shipping_date_sort_order'])) {
			$data['shipping_date_sort_order'] = $this->request->post['shipping_date_sort_order'];
		} else {
			$data['shipping_date_sort_order'] = $this->config->get('shipping_date_sort_order');
		}

		if (isset($this->request->post['shipping_date_delivery_price'])) {
			$data['shipping_date_delivery_price'] = $this->request->post['shipping_date_delivery_price'];
		} else {
			$data['shipping_date_delivery_price'] = $this->config->get('shipping_date_delivery_price');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/date', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/date')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
