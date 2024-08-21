<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ServicesOffered extends CI_Controller
{
    public function index()
    {
        $data['title'] = 'Services Offered';
        main_template('services-offered/services_offered', $data);
    }

    public function authorization_to_utilize_confidential_funds()
    {
        $data['title'] = 'Services Offered - Authorization to Utilize Confidential Funds';
        main_template('services-offered/authorization_to_utilize_confidential_funds', $data);
    }

    public function authority_to_lgus_for_the_purchase_of_motor_vehicle()
    {
        $data['title'] = 'Services Offered - Authority to LGUs for the Purchase of Motor Vehicle';
        main_template('services-offered/authority_to_lgus_for_the_purchase_of_motor_vehicle', $data);
    }

    public function certificate_for_foreign_travel_authority_of_local_officials()
    {
        $data['title'] = 'Services Offered - Certificate for Foreign Travel Authority of Local Officials';
        main_template('services-offered/certificate_for_foreign_travel_authority_of_local_officials', $data);
    }

    public function certificate_of_incumbency()
    {
        $data['title'] = 'Services Offered - Certificate of Incumbency';
        main_template('services-offered/certificate_of_incumbency', $data);
    }

    public function certificate_of_services_rendered_for_csc_eligibility()
    {
        $data['title'] = 'Services Offered - Certificate of Services Rendered for CSC Eligibility';
        main_template('services-offered/certificate_of_services_rendered_for_csc_eligibility', $data);
    }

    public function death_benefit_claims()
    {
        $data['title'] = 'Services Offered - Death Benefit Claims';
        main_template('services-offered/death_benefit_claims', $data);
    }

    public function full_disclosure_policy_compliance_certificate()
    {
        $data['title'] = 'Services Offered - Full Disclosure Policy Compliance Certificate';
        main_template('services-offered/full_disclosure_policy_compliance_certificate', $data);
    }

    public function provision_of_technical_assistance()
    {
        $data['title'] = 'Services Offered - Provision of Technical Assistance';
        main_template('services-offered/provision_of_technical_assistance', $data);
    }
}
