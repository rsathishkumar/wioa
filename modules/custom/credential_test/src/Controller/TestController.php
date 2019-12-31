<?php
namespace Drupal\credential_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;

/**
 * Provides route responses for the Example module.
 */
class TestController extends ControllerBase {

   /**
   * Checks access for tool questions page.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function accessForm(AccountInterface $account) {
    if ($account->Id() > 0) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

  /**
   * Checks access for tool questions page.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResult
   */
  public function accessFormEdit(AccountInterface $account, $nid) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    if ($node->getType() == 'questions' && $account->Id() > 0 && $node->getOwnerId() == $account->Id()) {
      return AccessResult::allowed();
    }

    return AccessResult::forbidden();
  }

  /**
   * Returns a question form.
   *
   * @return array
   *   Form html.
   */
  public function questionForm() {
    $form = $this->form_html();

    $element = array(
      '#children' => $form,
    );
    $element['#attached']['library'][] = 'credential_test/external';
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $element['#attached']['drupalSettings']['credential_test']['node']['values'] = "";
    $element['#attached']['drupalSettings']['credential_test']['user']['uid'] = $user->get('uid')->value;
    $element['#attached']['drupalSettings']['credential_test']['user']['name'] = $user->get('name')->value;
    return $element;
  }

  /**
   * Returns a question form.
   *
   * @return array
   *   Form html.
   */
  public function questionEditForm($nid) {

    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    $node_values['q1'] = $node->get('field_q1')->value;
    $node_values['q2'] = $node->get('field_q2')->value;
    $node_values['q3'] = $node->get('field_q3')->value;
    $node_values['q4'] = $node->get('field_q4')->value;
    $node_values['q5'] = $node->get('field_q5')->value;
    $node_values['q7'] = $node->get('field_q7')->value;
    $node_values['q8'] = $node->get('field_q8')->value;
    $node_values['q9'] = $node->get('field_q9')->value;
    $node_values['q10'] = $node->get('field_q10')->value;
    $node_values['q11'] = $node->get('field_q11')->value;
    $node_values['q12'] = $node->get('field_q12')->value;
    $node_values['q13'] = $node->get('field_q13')->value;
    if(isset($node->field_school[0])){
      $node_values['school'] = $node->field_school[0]->toArray()['target_id'];
    }

    $form = $this->form_html($nid);

    $element = array(
      '#children' => $form,
    );
    $element['#attached']['library'][] = 'credential_test/external';
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());

    $element['#attached']['drupalSettings']['credential_test']['node']['values'] = $node_values;
    $element['#attached']['drupalSettings']['credential_test']['user']['uid'] = $user->get('uid')->value;
    $element['#attached']['drupalSettings']['credential_test']['user']['name'] = $user->get('name')->value;
    return $element;
  }

  /**
   * Returns a question form.
   *
   * @return array
   *   Form html.
   */
  public function finishForm() {
    $output = "<div class='finish-message'><strong>Congratulations! You are ready to start working on a planning grant application for an ACE restart. If you would rather explore a Texas Partnership with an outside non-profit, click <a href='#'>here</a><strong>.</div>";

    $element = array(
      '#children' => $output,
    );

    return $element;
  }

  private function form_html ($nid = '') {
    $form = '
    <div id="content-test"></div>
    <script type="text/babel">
        var Form = React.createClass({
        getInitialState: function() {
          return {
            firstValue: "",
            secondValue: "",
            thirdValue: "",
            fourthValue: "",
            fifthValue: "",
            submitValue: 0,
            messageValue: 0,
            buttonValue: 0
          }
        },
        handleFirstLevelChange: function (event) {
          this.setState({
            firstValue: event.target.value,
            secondValue: "",
            thirdValue: "",
            fourthValue: "",
            fifthValue: "",
            submitValue: 0,
            messageValue: 0,
            buttonValue: 0
         });
        },
        handleSecondLevelChange: function (event) {
          this.setState({
            secondValue: event.target.value,
            thirdValue: "",
            fourthValue: "",
            fifthValue: "",
            submitValue: 0,
            messageValue: 0,
            buttonValue: 0
         });
        },
        handleThirdLevelChange: function (event) {
          this.setState({
            thirdValue: event.target.value,
            fourthValue: "",
            fifthValue: "",
            submitValue: 0,
            messageValue: 0
          });

          if (event.target.value == "graduate_degree" || this.state.firstValue == "industry_wide_Technical") {
            this.setState({
              buttonValue: 0
            });
          }
          else {
            this.setState({
              buttonValue: 1
            });
          }
        },
        handleFourthLevelChange: function (event) {
          this.setState({
            fourthValue: event.target.value,
          });

          if (this.state.firstValue == "industry_wide_Technical" || event.target.value == "") {
            this.setState({
              buttonValue: 0
            });
          }
          else {
            this.setState({
              buttonValue: 1
            });
          }
       },
        handleFifthLevelChange: function (event) {
          this.setState({
            fifthValue: event.target.value
          });

          if (event.target.value == "") {
            this.setState({
              buttonValue: 0
            });
          }
          else {
            this.setState({
              buttonValue: 1
            });
          }
        },
        getSecondLevelField: function () {
  	      if (!this.state.firstValue) {
    	    return null;
          }

          return (
            <div>
              <label>What type of institution is providing the credential?</label>
              <select onChange={this.handleSecondLevelChange} value={this.state.secondValue} id="type-of-institution" name="type_of_institution">
                <option value="">---</option>
                <option value="state_agency">State Agency</option>
                <option value="higher_education">Institution of Higher Education</option>
                <option value="native_american">Indian/Native American Tribal Organization</option>
                <option value="professional_organization">Professional/Industry/Employer Organization</option>
                <option value="recognized_state">ETA Office of Apprenticeship or Recognized State Apprenticeship Agency</option>
                <option value="public_regulatory">Public Regulatory Agency</option>
                <option value="veterans_affairs">Public Regulatory Agency</option>
                <option value="job_corps">Job Corps</option>
                <option value="other">Other</option>
              </select>
            </div>
          )
        },
        getThirdLevelField: function () {
  	      if (!this.state.secondValue) {
    	    return null;
          }

          return (
            <div>
            <label>What type of credential is it?</label>
            <select onChange={this.handleThirdLevelChange} value={this.state.thirdValue} id="type-of-credential" name="type_of_credential">
              <option value="">---</option>
              <option value="secondary_school">Secondary School Diploma/Equivalent</option>
              <option value="associate_degree">Associate Degree</option>
              <option value="bachelor_degree">Bachelor Degree</option>
              <option value="occupational_licensure">Occupational Licensure</option>
              <option value="occupational_certificate">Occupational Certificate (including Registered Apprenticeship and Career and Technical Education certificates)</option>
              <option value="occupational_certification">Occupational Certification</option>
              <option value="other_industry">Other Industry/Occupational Skills Completion</option>
              <option value="other">Other</option>
              <option value="graduate_degree">Graduate Degree (VR-only)</option>
            </select>
            </div>
          )
        },
        getFourthLevelField: function () {
  	      if (this.state.thirdValue != "graduate_degree") {
    	    return null;
          }

          return (
            <div>
            <label>Is the participant receiving training/education through VR?</label>
    	    <select onChange={this.handleFourthLevelChange} value={this.state.fourthValue} id="education-vr" name="education_through_vr">
       	    <option value="">---</option>
      	    <option value="Yes">Yes</option>
      	    <option value="No">No</option>
            </select>
            </div>
          )
        },
        getFifthLevelField: function () {
  	      if (this.state.firstValue != "industry_wide_Technical") {
    	    return null;
          }
          else if (this.state.thirdValue == "") {
            return null;
          }
          else if (this.state.thirdValue != "" && this.state.thirdValue == "graduate_degree" && this.state.fourthValue == "") {
            return null;
          }

          return (
            <div>
            <label>Is the training related to an in-demand industry in the local area?</label>
    	    <select onChange={this.handleFifthLevelChange} value={this.state.fifthValue} id="in-demand-industry" name="in_demand_industry">
       	    <option value="">---</option>
      	    <option value="Yes">Yes</option>
      	    <option value="No">No</option>
            </select>
            </div>
          )
        },
        getFirstValueMessage: function () {
  	      if (this.state.firstValue != "industry_wide_Technical" && this.state.firstValue != "") {
            this.setState({
              messageValue: 1
            });
    	    return ( <div id="message">A recognized postsecondary credential is awarded in recognition of an individual\'s attainment of measurable technical or industry/occupational skills necessary to obtain employment or advance within an industry/occupation.</div> );
          }
          return null;
        },
        getSecondValueMessage: function () {
  	      if (this.state.secondValue == "other" && this.state.secondValue != "") {
    	    return ( <div id="message">WARNING: States should review credentials carefully to ensure that institutions not on the list of credential-issuing instutions from the list defined in WIOA Joint Performance Guidance (TEGL 10-16) are meeting the WIOA definitions.</div> );
          }
          return null;
        },
        getThirdValueMessage: function () {
  	      if (this.state.thirdValue == "other" && this.state.thirdValue != "") {
            this.setState({
              messageValue: 1
            });
    	    return ( <div id="message">Credentials other than those listed do not count as a success in the Credential Attainment Numerator, even though there are cases where they may be useful and/or necessary for the participant.</div> );
          }
          return null;
        },
        getFourthValueMessage: function () {
  	      if (this.state.thirdValue == "graduate_degree" && this.state.fourthValue == "No") {
            this.setState({
              messageValue: 1
            });
    	    return ( <div id="message">Graduate Degrees can only count as a success for the WIOA title IV Vocational Rehabilition program.</div> );
          }
          return null;
        },
        getFifthValueMessage: function () {
  	      if (this.state.firstValue == "industry_wide_Technical" && this.state.fifthValue == "No") {
    	    return ( <div id="message">While this may count as a credential, note that WIOA title I funds can only be used to pay for training that is related to an in-demand industry or occupation.</div> );
          }
          return null;
        },
        getMessage: function () {
          var wioa = false;
          var message, message2, message3, message4, message5, warning = "";

          if (this.state.submitValue == 1) {
            if (this.state.submitValue && (!this.state.firstValue || !this.state.secondValue || !this.state.thirdValue)) {
              return ( <div id="message">Incomplete Response</div> );
            }
            else if (this.state.messageValue == 1) {
              return ( <div id="message">Not a WIOA Post Secondary Credential.</div> );
            }
            else {
              return ( <div id="message">WIOA Post Secondary Credential.</div> );
            }
          }


          return null;
        },
        showButton: function (event) {
          if (this.state.buttonValue == 1) {
            return ( <button>Send data!</button>);
          }
        },
        registerUser: function (event) {
          event.preventDefault();
          const data = new FormData(event.target);
          this.setState({
            submitValue: 1
          });
          if (!this.state.firstValue || !this.state.secondValue || !this.state.thirdValue) {
            return null;
          }
          return fetch("/tool/questions/save", {
            method: "POST",
            body: data
          });
        },
        render: function() {
          return (
          <form onSubmit={this.registerUser} id="credential_form">
            <div>
              <label>What type of skills does it result in?</label>
              <select onChange={this.handleFirstLevelChange} value={this.state.firstValue} id="type-of-skill" name="type_of_skill">
                <option value="">---</option>
                <option value="industry_wide_Technical">Industry-wide Technical or Industry/Occupational Skills</option>
                <option value="general_safety">General Safety Skills</option>
                <option value="work_readiness">Work Readiness Skills</option>
                <option value="hygiene_skills">Hygiene Skills</option>
                <option value="general_computer">General Computer Skills (e.g. word/excel/outlook/PPT/etc)</option>
                <option value="other_general">Other General Skills</option>
              </select>
            </div>
            {this.getFirstValueMessage()}
            {this.getSecondLevelField()}
            {this.getSecondValueMessage()}
            {this.getThirdLevelField()}
            {this.getThirdValueMessage()}
            {this.getFourthLevelField()}
            {this.getFourthValueMessage()}
            {this.getFifthLevelField()}
            {this.getFifthValueMessage()}
            {this.showButton()}
            {this.getMessage()}
            </form>
          )
        }
      });

      ReactDOM.render(
        <Form/>,
        document.getElementById("content-test")
      );
    </script>';

    return $form;
  }

  public function questionSaveForm() {
    $type_of_skill = \Drupal::request()->request->get('type_of_skill');
    $type_of_institution = \Drupal::request()->request->get('type_of_institution');
    $type_of_credential = \Drupal::request()->request->get('type_of_credential');
    $education_through_vr = \Drupal::request()->request->get('education_through_vr');
    $in_demand_industry = \Drupal::request()->request->get('in_demand_industry');

    $user = \Drupal::currentUser();
    $node = Node::create([
      'type'  => 'credential_test',
      'title' => 'Submitted by user: ' . $user->getAccountName(),
      'field_education_through_vr' => $education_through_vr,
      'field_in_demand_industry' => $in_demand_industry,
      'field_type_of_credential' => $type_of_credential,
      'field_type_of_institution' => $type_of_institution,
      'field_type_of_skills' => $type_of_skill
    ]);
    $node->save();
  }

}
