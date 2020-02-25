<?php
namespace Drupal\credential_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;
use Fpdf\Fpdf;

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
            firstLabel: "",
            secondValue: "",
            secondLabel: "",
            thirdValue: "",
            fourthValue: "",
            fourthLabel: "",
            fifthValue: "",
            submitValue: 0,
            buttonValue: 0
          }
        },
        handleFirstLevelChange: function (event) {
          let index = event.nativeEvent.target.selectedIndex;
          this.setState({
            firstValue: event.target.value,
            firstLabel: event.nativeEvent.target[index].text,
            secondValue: "",
            secondLabel: "",
            thirdValue: "",
            fourthValue: "",
            fourthLabel: "",
            fifthValue: "",
            submitValue: 0,
            buttonValue: 0
         });
        },
        handleSecondLevelChange: function (event) {
          let index = event.nativeEvent.target.selectedIndex;
          this.setState({
            secondValue: event.target.value,
            secondLabel: event.nativeEvent.target[index].text,
            thirdValue: "",
            fourthValue: "",
            fourthLabel: "",
            fifthValue: "",
            buttonValue: 0
         });
        },
        handleThirdLevelChange: function (event) {
          this.setState({
            thirdValue: event.target.value,
            fourthValue: "",
            fourthLabel: "",
            fifthValue: "",
            buttonValue: 0
          });
        },
        handleFourthLevelChange: function (event) {
          let index = event.nativeEvent.target.selectedIndex;
          this.setState({
            fourthValue: event.target.value,
            fourthLabel: event.nativeEvent.target[index].text,
            fifthValue: ""
          });

          if (event.target.value == "industry_wide_Technical") {
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
  	      if (!this.state.firstValue || this.state.firstValue == "other") {
    	    return null;
          }

          return (
            <div className="questions">
              <label>What type of credential is it?</label>
              <select onChange={this.handleSecondLevelChange} value={this.state.secondValue} id="type-of-credential" name="type_of_credential">
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
        getThirdLevelField: function () {
  	      if (this.state.secondValue != "graduate_degree") {
    	    return null;
          }

          return (
            <div className="questions">
            <label>Is the participant receiving training/education through VR?</label>
    	    <select onChange={this.handleThirdLevelChange} value={this.state.thirdValue} id="education-vr" name="education_through_vr">
       	    <option value="">---</option>
      	    <option value="Yes">Yes</option>
      	    <option value="No">No</option>
            </select>
            </div>
          )
        },
        getFourthLevelField: function () {
  	      if (!this.state.secondValue && this.state.firstValue != "other") {
    	    return null;
          }
          else if (this.state.secondValue == "graduate_degree" && this.state.thirdValue == "") {
            return null;
          }

          return (
            <div className="questions">
              <label>What type of Skills does it attest to?</label>
              <select onChange={this.handleFourthLevelChange} value={this.state.fourthValue} id="type-of-skill" name="type_of_skill">
                <option value="">---</option>
                <option value="industry_wide_Technical">Industry-wide Technical or Industry/Occupational Skills</option>
                <option value="general_safety">General Safety Skills</option>
                <option value="work_readiness">Work Readiness Skills</option>
                <option value="hygiene_skills">Hygiene Skills</option>
                <option value="general_computer">General Computer Skills (e.g. word/excel/outlook/PPT/etc)</option>
                <option value="other_general">Other General Skills</option>
              </select>
            </div>
          )
        },
        getFifthLevelField: function () {
  	      if (this.state.fourthValue != "industry_wide_Technical") {
    	    return null;
          }

          return (
            <div className="questions">
            <label>Is the training related to an in-demand industry/occupation in the local area?</label>
    	    <select onChange={this.handleFifthLevelChange} value={this.state.fifthValue} id="in-demand-industry" name="in_demand_industry">
       	    <option value="">---</option>
      	    <option value="Yes">Yes</option>
      	    <option value="No">No</option>
            </select>
            </div>
          )
        },
        getFirstValueMessage: function () {
  	      if (this.state.firstValue == "other" && this.state.firstValue != "") {
    	    return ( <div id="firstmessage" className="messages">Warning: Other organizations not listed may award credentials. If "other" confirm that the organization awards recognized credentials.</div> );
          }
          return null;

        },
        getSecondValueMessage: function () {
  	      if (this.state.secondValue == "other" && this.state.secondValue != "") {
    	    return ( <div id="secondmessage" className="messages">Credentials other than those listed do not count as a success in the Credential Attainment Numerator, even though there are cases where they may be useful and/or necessary for the participant.</div> );
          }
          return null;
        },
        getThirdValueMessage: function () {
  	      if (this.state.secondValue == "graduate_degree" && this.state.thirdValue == "No") {
    	    return ( <div id="thirdmessage" className="messages">Graduate Degrees can only count as a success for the WIOA title IV Vocational Rehabilition program.</div> );
          }
          return null;
        },
        getFourthValueMessage: function () {
  	      if (this.state.fourthValue != "industry_wide_Technical" && this.state.fourthValue != "") {
    	    return ( <div id="fourthmessage" className="messages">A recognized postsecondary credential is awarded in recognition of an individual\'s attainment of measurable technical or industry/occupational skills necessary to obtain employment or advance within an industry/occupation.</div> );
          }
          return null;
        },
        getFifthValueMessage: function () {
  	      if (this.state.fourthValue == "industry_wide_Technical" && this.state.fifthValue == "No") {
    	    return ( <div id="fifthmessage" className="messages">While this may count as a credential, note that WIOA title I funds can only be used to pay for training that is related to an in-demand industry or occupation.</div> );
          }
          return null;
        },
        getMessage: function () {
          var incomplete = "";

          if (this.state.buttonValue != 1) {
            return null;
          }
          else if (this.state.fourthValue != "industry_wide_Technical" || this.state.secondValue == "other" || (this.state.secondValue == "graduate_degree" && this.state.thirdValue == "No")) {
            return ( <div id="lastmessage" className="error">Not a WIOA Post Secondary Credential.</div> );
          }
          else {
            return ( <div id="lastmessage" className="success">WIOA Post Secondary Credential.</div> );
          }

          return null;
        },
        showPrintButton: function (event) {
          if (this.state.buttonValue == 1) {
            return ( <button onClick={this.handlePrintButtonClick}>Export PDF</button>);
          }
          return null;
        },
        handleCSVButtonClick: function (event) {
          event.preventDefault();
          var csv = [];

          var rows = document.querySelectorAll("div.questions");

          for(var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll("label, select");
            for(var j = 0; j < cols.length; j++) {
                if (cols[j].type == "select-one") {
                  row.push("\""+cols[j].selectedOptions[0].label+"\"");
                }
                else {
                   row.push("\""+cols[j].innerText+"\"");
                }
            }
            var warnings = rows[i].nextElementSibling;
            if (warnings.classList.contains("messages")) {
              row.push("\"" + warnings.innerText + "\"");
            }
            csv.push(row.join(","));
          }

          csv.push(jQuery("#lastmessage").html());

          var csvcontent = csv.join("\r\n");

          var csvFile;
          var downloadLink;

          if (window.Blob == undefined || window.URL == undefined || window.URL.createObjectURL == undefined) {
              alert("Your browser doesnt support Blobs");
              return;
          }

          csvFile = new Blob([csvcontent], {type:"text/csv"});
          downloadLink = document.createElement("a");
          downloadLink.download = "tools.csv";
          downloadLink.href = window.URL.createObjectURL(csvFile);
          downloadLink.style.display = "none";
          document.body.appendChild(downloadLink);
          downloadLink.click();
        },
        handlePrintButtonClick: function (event) {
          event.preventDefault();
          var pdf = new jsPDF("p", "pt", "letter");

          var i = 40;
          pdf.setFontSize(11);
          var source = "<div>What type of organization or institution is offering the training program?</div>";
          source += "<div>" + this.state.firstLabel + "</div>";
          if (jQuery("#firstmessage").length > 0) {
            source += jQuery("#firstmessage").html();
          }

          if (this.state.secondLabel) {
            source += "<div>What type of credential is it?</div>";
            source += "<div>" + this.state.secondLabel + "</div>";
            if (jQuery("#secondmessage").length > 0) {
              source += jQuery("#secondmessage").html();
            }
          }

          if (this.state.thirdValue) {
            source += "<div>Is the participant receiving training/education through VR?</div>";
            source += "<div>" + this.state.thirdValue + "</div>";
            if (jQuery("#thirdmessage").length > 0) {
              source += jQuery("#thirdmessage").html();
            }
          }

          if (this.state.fourthLabel) {
            source += "<div>What type of Skills does it attest to?</div>";
            source += "<div>" + this.state.fourthLabel + "</div>";
            if (jQuery("#fourthmessage").length > 0) {
              source += jQuery("#fourthmessage").html();
            }
          }

          if (this.state.fifthValue) {
            source += "<div>Is the training related to an in-demand industry/occupation in the local area?</div>";
            source += "<div>" + this.state.fifthValue + "</div>";
            if (jQuery("#fifthmessage").length > 0) {
              source += jQuery("#fifthmessage").html();
            }
          }

          if (jQuery("#lastmessage").length > 0) {
            source += jQuery("#lastmessage").html();
          }

          var margins = {
            top: 40,
            bottom: 60,
            left: 40,
            width: 522
          };
          pdf.fromHTML(
            source, // HTML string or DOM elem ref.
            margins.left, // x coord
            margins.top, {
              // y coord
              width: margins.width // max width of content on PDF
            },
            function(dispose) {
              // dispose: object with X, Y of the last line add to the PDF
              //          this allow the insertion of new lines after html
              pdf.save("tools.pdf");
            },
            margins
          );
        },
        showCSVButton: function (event) {
          if (this.state.buttonValue == 1) {
            return ( <button onClick={this.handleCSVButtonClick}>Export CSV</button>);
          }
        },
        render: function() {
          return (
          <form onSubmit={this.registerUser} id="credential_form" method="post" target="_blank">
            <div className="questions">
              <label>What type of organization or institution is offering the training program?</label>
              <select onChange={this.handleFirstLevelChange} value={this.state.firstValue} id="type-of-institution" name="type_of_institution">
                <option value="">---</option>
                <option value="state_agency">State Agency</option>
                <option value="higher_education">Institution of Higher Education</option>
                <option value="native_american">Indian/Native American Tribal Organization</option>
                <option value="professional_organization">Professional/Industry/Employer Organization</option>
                <option value="recognized_state">ETA Office of Apprenticeship or Recognized State Apprenticeship Agency</option>
                <option value="public_license">Public RegulatoryAgency/Government Licensing Entity</option>
                <option value="veterans_affairs">Dept of Veteran\'s Affairs (VA)-approved program</option>
                <option value="job_corps">Job Corps</option>
                <option value="other">Other</option>
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
            {this.showPrintButton()}
            {this.showCSVButton()}
            <div id="pdflink"></div>
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
}
