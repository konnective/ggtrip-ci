<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()
	{
		if($this->input->is_ajax_request()) {
			return $this->sendEmail();
		} else {
			return $this->load->view('welcome_message');
		}
	}

	public function error_404()
	{
        return $this->load->view('error_404');
	}

	protected function sendEmail()
	{
        if($this->input->post('type') === 'quote') {
            $this->form_validation->set_rules([
                [
                    'field' => 'phone',
                    'label' => 'Phone',
                    'rules' => 'required|numeric',
                    'errors' => [
                        'required' => "%s is required",
                        'numeric' => "%s is invalid",
                        'min_length' => "Min 10 chars should be in %s",
                        'max_length' => "Max 12 chars allowed for %s"
                    ],
                ],
                [
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'required|max_length[200]|valid_email',
                    'errors' => [
                        'required' => "%s is required",
                        'valid_email' => "%s is invalid",
                        'max_length' => "Max 200 chars allowed"
                    ],
                ]
            ]);
        } elseif($this->input->post('type') === 'subscribe') {
            $this->form_validation->set_rules([
                [
                    'field' => 'full_name',
                    'label' => 'Full name',
                    'rules' => 'required|max_length[200]',
                    'errors' => [
                        'required' => "%s is required",
                        'max_length' => "Max 200 chars allowed for %s"
                    ],
                ],
                [
                    'field' => 'email',
                    'label' => 'Email',
                    'rules' => 'required|max_length[200]|valid_email',
                    'errors' => [
                        'required' => "%s is required",
                        'valid_email' => "%s is invalid",
                        'max_length' => "Max 200 chars allowed"
                    ],
                ]
            ]);
        } else {
            $this->form_validation->set_rules($this->validate);
        }

		if ($this->form_validation->run() == FALSE) {
			responseMsg(false, strip_tags(validation_errors()), null, true);
		} else {
			$postArray = [
				'type' => $this->input->post('type'),
				'departure_date' => $this->input->post('departure_date'),
				'arrival_date' => $this->input->post('arrival_date'),
				'departure_place' => $this->input->post('departure_place'),
				'arrival_place' => $this->input->post('arrival_place'),
				'full_name' => $this->input->post('full_name'),
				'passengers' => $this->input->post('passengers'),
				'email' => $this->input->post('email'),
				'phone' => $this->input->post('phone'),
				'details' => $this->input->post('details'),
			];

			if(!empty($_FILES['image']['name'])) {
                $image = $this->uploadImage('image');

                if($image['error']) {
                    responseMsg(false, $image['message']);
                }

                $postArray['images'] = $image['message'];
            }

			$id = $this->generalmodel->add($postArray, 'inquiries');

			if($id){
                $this->sendFinalEmail($postArray);
				responseMsg(true, '', "https://gogotripsus.com/thank-you/");
			} else {
				responseMsg(false, 'Something went wrong!');
			}
		}
	}

    public function sendFinalEmail($formData)
    {
        $body = "<h3>" . ($formData['type'] === 'quote' ? 'New Quote Request' : 'New Trip Inquiry') . "</h3>";
        $subject = $formData['type'] === 'quote' ? 'New Quote Request' : 'New Trip Inquiry';

        if ($formData['type'] === 'quote') {
            $body .= "<p><strong>Type:</strong> " . $formData['type'] . "</p>";
            $body .= "<p><strong>Email:</strong> " . $formData['email'] . "</p>";
            $body .= "<p><strong>Phone:</strong> +1" . $formData['phone'] . "</p>";
        } else if ($formData['type'] === 'subscribe') {
            $body .= "<p><strong>Type:</strong> " . $formData['type'] . "</p>";
            $body .= "<p><strong>Email:</strong> " . $formData['email'] . "</p>";
            $body .= "<p><strong>Name:</strong> " . $formData['full_name'] . "</p>";
        } else {
            $body .= "<p><strong>Trip Type:</strong> " . $formData['type'] . "</p>";
            $body .= "<p><strong>Name:</strong> " . $formData['full_name'] . "</p>";
            $body .= "<p><strong>Email:</strong> " . $formData['email'] . "</p>";
            $body .= "<p><strong>Phone:</strong> +1" . $formData['phone'] . "</p>";
            $body .= "<p><strong>Departure Date:</strong> " . $formData['departure_date'] . "</p>";
            $body .= "<p><strong>Arrival Date:</strong> " . ($formData['arrival_date'] ?? '') . "</p>";
            $body .= "<p><strong>Departure Place:</strong> " . $formData['departure_place'] . "</p>";
            $body .= "<p><strong>Arrival Place:</strong> " . $formData['arrival_place'] . "</p>";
            $body .= "<p><strong>Passengerse:</strong> " . $formData['passengers'] . "</p>";
            $body .= "<p><strong>Details:</strong> " . $formData['details'] . "</p>";
        }
        
        $body .= "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        
        $creds = $this->config->item('emails')['info'];
        $email = [
            'info@gogotripsus.com',
        ];

        $this->load->library('email');
        $this->email->initialize($creds);
        $this->email->clear(TRUE);
        $this->email->from($creds['smtp_user'], APP_NAME);
        $this->email->to($email);
		$this->email->subject($subject);
		$this->email->message($body);

		if (!empty($formData['images'])) {
            $image = $this->path.$formData['images'];

            if(is_file($image)) {
                $this->email->attach($_SERVER['DOCUMENT_ROOT'] . str_replace(basename($_SERVER["SCRIPT_NAME"]), "", $_SERVER["SCRIPT_NAME"]) . $image);
            }
        }

        $emailSent = $this->email->send(FALSE);

        $mailData = [
            'attachment'    => $formData['images'] ?? '',
            'to_email'      => implode(',', $email),
            'subject'       => $subject,
            'email_content' => $body,
            'status'        => $emailSent ? 'Sent' : 'Failed',
            'email_log'     => $this->email->print_debugger(array('headers')),
        ];

        $query = $this->generalmodel->add($mailData, "email_logs");

        $this->email->clear();

        return $emailSent;
    }

    public function arrival_date($str)
    {
        if($this->input->post('type') === 'one-trip') {
            return true;
        }

        if (empty($str)) {
            $this->form_validation->set_message('arrival_date', "%s is invalid!");
            return false;
        }

        return true;
    }

	private $validate = [
        [
            'field' => 'type',
            'label' => 'Type',
            'rules' => 'required',
            'errors' => [
                'required' => "%s is required",
            ],
        ],
        [
            'field' => 'departure_date',
            'label' => 'Departure date',
            'rules' => 'required|max_length[10]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 10 chars allowed for %s"
            ],
        ],
        [
            'field' => 'arrival_date',
            'label' => 'Arrival date',
            'rules' => 'callback_arrival_date|max_length[10]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 10 chars allowed for %s"
            ],
        ],
        [
            'field' => 'departure_place',
            'label' => 'Departure place',
            'rules' => 'required|max_length[200]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 200 chars allowed for %s"
            ],
        ],
        [
            'field' => 'arrival_place',
            'label' => 'Arrival place',
            'rules' => 'required|max_length[200]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 200 chars allowed for %s"
            ],
        ],
        [
            'field' => 'full_name',
            'label' => 'Full name',
            'rules' => 'required|max_length[200]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 200 chars allowed for %s"
            ],
        ],
        [
            'field' => 'details',
            'label' => 'Details',
            'rules' => 'max_length[500]',
            'errors' => [
                'required' => "%s is required",
                'max_length' => "Max 500 chars allowed for %s"
            ],
        ],
        [
            'field' => 'phone',
            'label' => 'Phone',
            'rules' => 'required|numeric',
            'errors' => [
                'required' => "%s is required",
                'numeric' => "%s is invalid",
                'min_length' => "Min 10 chars should be in %s",
                'max_length' => "Max 12 chars allowed for %s"
            ],
        ],
        [
            'field' => 'passengers',
            'label' => 'Passengers',
            'rules' => 'required|numeric',
            'errors' => [
                'required' => "%s is required",
                'numeric' => "%s is invalid",
            ],
        ],
        [
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|max_length[200]|valid_email',
            'errors' => [
                'required' => "%s is required",
                'valid_email' => "%s is invalid",
                'max_length' => "Max 200 chars allowed"
            ],
        ]
    ];
}