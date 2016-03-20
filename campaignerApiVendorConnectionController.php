<?php


class campaignerApiVendorConnectionController extends campaignerApiBaseController{


	function apiImmediateUpload($contact)
	{
		/*
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1
				Host: ws.campaigner.com
				Content-Type: text/xml; charset=utf-8
				Content-Length: length
				SOAPAction: "https://ws.campaigner.com/2013/01/ImmediateUpload"
		 */
		// Required:  "addToGroup" which will loop through the COMMA delimited numbers and iterate as "<int>XXX</int><int>..."

		// Integers of GROUP ID -- requires at least one valid list ID
		//  $contact=$contact[0];
		$addToGroupArr = @$contact['addToGroupArr'];

		if (count($addToGroupArr)<1) {
			echo "<p>No group provided, skipping this contact record. (".print_r($contact,true)."</p>";
			//  return false;
		}

		$addToGroupXmlArr = array();
		foreach ($addToGroupArr as $groupId) {
			$addToGroupXmlArr[] = "<ns:int>$groupId</ns:int>";
		}

		$addToGroupXml = implode(" ", $addToGroupXmlArr);

		// Below was working -- remove <ContactId>  node and <ContactUniqueIdentifier> was "Email"

		//TODO: Convert this to XML Parser or use SOAP object?  Good luck conforming it right.
		// SOAP 1.1, managed to get a response, unlike SOAP 1.2

		 $soapXml='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
					   <soapenv:Header/>
					   <soapenv:Body>
					      <ns:ImmediateUpload>
					         <ns:authentication>
					            <ns:Username>'.$this->apiUserName.'</ns:Username><ns:Password>'.$this->apiPassword.'</ns:Password>
					         </ns:authentication>
					         <ns:UpdateExistingContacts>true</ns:UpdateExistingContacts>
					         <ns:TriggerWorkflow>false</ns:TriggerWorkflow>
					         <ns:contacts>
					            <ns:ContactData>
									<ns:EmailAddress IsNull="false">' . $contact['emailAddress'] . '</ns:EmailAddress>

							          <ns:FirstName>' . $contact['firstName'] . '</ns:FirstName>
							          <ns:LastName>' . $contact['lastName'] . '</ns:LastName>

					               <ns:CustomAttributes>
						               <ns:CustomAttribute Id="101294" IsNull="false">'.$contact['state'].'</ns:CustomAttribute>
						               <ns:CustomAttribute Id="5141268" IsNull="false">'.$contact['birthday_month'].'</ns:CustomAttribute>
					                   <ns:CustomAttribute Id="5141253" IsNull="false">'.$contact['license_expiration_date'].'</ns:CustomAttribute>
					               </ns:CustomAttributes>

					               <ns:ContactKey>
					                  <ns:ContactId>0</ns:ContactId><ns:ContactUniqueIdentifier>' . $contact['emailAddress'] . '</ns:ContactUniqueIdentifier>
					               </ns:ContactKey>
					                <ns:AddToGroup>' . $addToGroupXml . '</ns:AddToGroup>
					            </ns:ContactData>
					         </ns:contacts>
					      </ns:ImmediateUpload>
					   </soapenv:Body>
					</soapenv:Envelope>';


		$headerArr   = $this->buildApiHeaders($soapXml);


		// listmanagement.asmx
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiImmediateUploadUpdate($contact)
	{
		/*
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1
				Host: ws.campaigner.com
				Content-Type: text/xml; charset=utf-8
				Content-Length: length
				SOAPAction: "https://ws.campaigner.com/2013/01/ImmediateUpload"
		 */
		// Required:  "addToGroup" which will loop through the COMMA delimited numbers and iterate as "<int>XXX</int><int>..."

		// Integers of GROUP ID -- requires at least one valid list ID
		//  $contact=$contact[0];
		$addToGroupXml = $this->buildGroupNode(@$contact['addToGroupArr']);


		//TODO: Convert this to XML Parser or use SOAP object?  Good luck conforming it right.
		// SOAP 1.1, managed to get a response, unlike SOAP 1.2
		$soapXml = '<?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			  <soap:Body>
			    <ImmediateUpload xmlns="https://ws.campaigner.com/2013/01">
					    ' .$this->getAuthenticationNode(). '
				   <UpdateExistingContacts>true</UpdateExistingContacts>
				   <TriggerWorkflow>false</TriggerWorkflow>
			       <contacts>
				        <ContactData>
				          <ContactKey>
							 <ContactUniqueIdentifier>Email</ContactUniqueIdentifier>
				          </ContactKey>
				          <EmailAddress>' . $contact['emailAddress'] . '</EmailAddress>
				          <FirstName>' . $contact['firstName'] . '</FirstName>
				          <LastName>' . $contact['lastName'] . '</LastName>
				          <Status>Subscribed</Status>
				          <MailFormat>HTML</MailFormat>
				          <IsTestContact>false</IsTestContact>
				          <AddToGroup>' . $addToGroupXml . '</AddToGroup>
				           <CustomAttributes>
							 <CustomAttributeId>101294</CustomAttributeId>
							 <CustomAttributeValue>'.$contact['state'].'</CustomAttributeValue>
						  </CustomAttributes>
				        </ContactData>
			       </contacts>
			    </ImmediateUpload>
			  </soap:Body>
			</soap:Envelope>';


		$headerArr   = $this->buildApiHeaders($soapXml);
		//  $headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/ImmediateUpload\"";

		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiListFromEmails(){

		//  POST /2013/01/campaignmanagement.asmx HTTP/1.1 Host: ws.campaigner.com Content-Type: text/xml; charset=utf-8 Content-Length: length
		//  SOAPAction: "https://ws.campaigner.com/2013/01/ListFromEmails"

		$email='@gmail.com';
		//  '.$this->fetchSearchXml($email).'

		$soapXml='<?xml version="1.0" encoding="utf-8"?>
		<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body>
				<ListFromEmails xmlns="https://ws.campaigner.com/2013/01">
					'.$this->getAuthenticationNode().'
				</ListFromEmails>
			</soap:Body>
		</soap:Envelope>';

		$headerArr   = $this->buildApiHeadersSoap($soapXml);
		
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiContactDetailsReport($reportType='rpt_Contact_Details',$email=''){

		/**
		 *
		 *
		 *  POST /2013/01/contactmanagement.asmx HTTP/1.1 Host: ws.campaigner.com Content-Type: text/xml; charset=utf-8 Content-Length: length
		 *  SOAPAction: "https://ws.campaigner.com/2013/01/DownloadReport
		 *
		 *  <reportTicketId>string</reportTicketId>
			<fromRow>int</fromRow>
			<toRow>int</toRow>
		 *
		 * '.$this->fetchSearchXml($email).'
		 */

		$soapXml='<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
						<soap:Body>
							<DownloadReport xmlns="https://ws.campaigner.com/2013/01/">
								'.$this->getAuthenticationNode().'
								<reportType>'.$reportType.'</reportType>
								<fromRow>1</fromRow>
								<toRow>10</toRow>
							</DownloadReport>
						</soap:Body>
					</soap:Envelope>';

	
		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/DownloadReport\"";

		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);
	}


	function apiGetContactsByEmail($email=''){
		/*
		 *      For some reason, this is UNDOCUMENTED in Campaigners documentation PDF
		 *      but online at https://ws.campaigner.com/2013/01/contactmanagement.asmx?op=GetContacts
		 *
		 *      Works with legitimate ContactID number, which can be very long, BTW!
		 *
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1
					Host: ws.campaigner.com
					Content-Type: text/xml; charset=utf-8
					Content-Length: length
					SOAPAction: "https://ws.campaigner.com/2013/01/GetContacts"
		 */


		/** FROM CAMPAIGNER API TECH:
		 *
		$soapXml='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <ns:GetContacts>
				         <ns:authentication>
				            <ns:Username>username</ns:Username><ns:Password>password</ns:Password>
				         </ns:authentication>
				         <ns:contactFilter>
				            <ns:ContactKeys>
				               <ns:ContactKey>
				                  <ns:ContactId>0</ns:ContactId><ns:ContactUniqueIdentifier>email@mymail.com</ns:ContactUniqueIdentifier>
				               </ns:ContactKey>
				            </ns:ContactKeys>
				         </ns:contactFilter>
				         <ns:contactInformationFilter>
				            <ns:IncludeStaticAttributes>false</ns:IncludeStaticAttributes>
				            <ns:IncludeCustomAttributes>false</ns:IncludeCustomAttributes>
				            <ns:IncludeSystemAttributes>false</ns:IncludeSystemAttributes>
				            <ns:IncludeGroupMembershipData>false</ns:IncludeGroupMembershipData>
				         </ns:contactInformationFilter>
				      </ns:GetContacts>
				   </soapenv:Body>
			</soapenv:Envelope>';
		*/

		$soapXml='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="https://ws.campaigner.com/2013/01">
					   <soapenv:Header/>
					   <soapenv:Body>
					      <ns:GetContacts>
					         <ns:authentication>
					            <ns:Username>'.$this->apiUserName.'</ns:Username><ns:Password>'.$this->apiPassword.'</ns:Password>
					         </ns:authentication>
					         <ns:contactFilter>
					            <ns:ContactKeys>
					               <ns:ContactKey>
					                  <ns:ContactId>0</ns:ContactId><ns:ContactUniqueIdentifier>'.$email.'</ns:ContactUniqueIdentifier>
					               </ns:ContactKey>
					            </ns:ContactKeys>
					         </ns:contactFilter>
					         <ns:contactInformationFilter>
					            <ns:IncludeStaticAttributes>false</ns:IncludeStaticAttributes>
					            <ns:IncludeCustomAttributes>false</ns:IncludeCustomAttributes>
					            <ns:IncludeSystemAttributes>false</ns:IncludeSystemAttributes>
					            <ns:IncludeGroupMembershipData>false</ns:IncludeGroupMembershipData>
					         </ns:contactInformationFilter>
					      </ns:GetContacts>
					   </soapenv:Body>
				</soapenv:Envelope>';

		/**
		$soapXml='<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					  <soap:Body>
					    <GetContacts>
					      '.$this->getAuthenticationNode().'
					      <contactFilter>
					         <ContactKeys>
					          <ContactKey>
					            <ns:ContactId>0</ns:ContactId><ns:ContactUniqueIdentifier>'.$email.'</ns:ContactUniqueIdentifier>
					          </ContactKey>
					        </ContactKeys>
					      </contactFilter>
					      <contactInformationFilter>
					        <IncludeStaticAttributes>false</IncludeStaticAttributes>
					        <IncludeCustomAttributes>false</IncludeCustomAttributes>
					        <IncludeSystemAttributes>false</IncludeSystemAttributes>
					        <IncludeGroupMembershipData>false</IncludeGroupMembershipData>
					      </contactInformationFilter>
					    </GetContacts>
					  </soap:Body>
					</soap:Envelope>';
		*/

		$headerArr   = $this->buildApiHeadersSoap($soapXml);
		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/GetContacts\"";
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiGetContactsById($id){
		/*
		 *      For some reason, this is UNDOCUMENTED in Campaigners horrible documentation PDF
		 *      but online at https://ws.campaigner.com/2013/01/contactmanagement.asmx?op=GetContacts
		 *
		 *      Works with legitimate ContactID number, which can be very long, BTW!
		 *
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1
					Host: ws.campaigner.com
					Content-Type: text/xml; charset=utf-8
					Content-Length: length
					SOAPAction: "https://ws.campaigner.com/2013/01/GetContacts"
		 */

		$soapXml='<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					  <soap:Body>
					    <GetContacts xmlns="https://ws.campaigner.com/2013/01">
					      '.$this->getAuthenticationNode().'
					      <contactFilter>
					        <ContactKeys>
					          <ContactKey>
					            <ContactId>'.$id.'</ContactId>
					            <ContactUniqueIdentifier>Email</ContactUniqueIdentifier>
					          </ContactKey>
					        </ContactKeys>
					      </contactFilter>
					      <contactInformationFilter>
					        <IncludeStaticAttributes>true</IncludeStaticAttributes>
					        <IncludeCustomAttributes>true</IncludeCustomAttributes>
					        <IncludeSystemAttributes>false</IncludeSystemAttributes>
					        <IncludeGroupMembershipData>true</IncludeGroupMembershipData>
					      </contactInformationFilter>
					    </GetContacts>
					  </soap:Body>
					</soap:Envelope>';

		$headerArr   = $this->buildApiHeaders($soapXml);
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiListAttributes(){
		/**
		 * POST /2013/01/contactmanagement.asmx HTTP/1.1
			Host: ws.campaigner.com
			Content-Type: text/xml; charset=utf-8
			Content-Length: length
			SOAPAction: "https://ws.campaigner.com/2013/01/ListAttributes"

		 */
			$soapXml='<?xml version="1.0" encoding="utf-8"?>
						<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
						  <soap:Body>
						    <ListAttributes xmlns="https://ws.campaigner.com/2013/01">
								'.$this->getAuthenticationNode().'
						      <filter>
						        <IncludeAllDefaultAttributes>true</IncludeAllDefaultAttributes>
						        <IncludeAllCustomAttributes>true</IncludeAllCustomAttributes>
						        <IncludeAllSystemAttributes>false</IncludeAllSystemAttributes>
						      </filter>
						    </ListAttributes>
						  </soap:Body>
						</soap:Envelope>';

		$headerArr   = $this->buildApiHeaders($soapXml);
		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/ListAttributes\"";

		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function apiListContactGroups($email){
	/**
	 *  POST /2013/01/listmanagement.asmx HTTP/1.1 Host: ws.campaigner.com Content-Type: text/xml; charset=utf-8 Content-Length: length
	 *  SOAPAction: "https://ws.campaigner.com/2013/01/ListContactGroups"
	 *
	 */

	    $soapXml='<?xml version="1.0" encoding="utf-8"?>
				<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					<soap:Body>
						<ListContactGroups xmlns="https://ws.campaigner.com/2013/01">
							'.$this->getAuthenticationNode().'
						</ListContactGroups>
						'.$this->fetchSearchXml($email).'
					</soap:Body>
				</soap:Envelope>';

		$headerArr   = $this->buildApiHeaders($soapXml);
		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/ListContactGroups\"";

		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function fetchSearchXml($email){

		$searchXml='<xmlContactQuery>
							<contactssearchcriteria>
								<set>Partial</set>
								<evaluatedefault>True</evaluatedefault>
								<group>
									<filter>
										<filtertype>SearchAttributeValue</filtertype>
										<staticattributeid>3</staticattributeid>
										<action>
											<type>Text</type>
											<operator>Containing</operator>
											<value>'.$email.'</value>
										</action>
									</filter>
								</group>
							</contactssearchcriteria>
						</xmlContactQuery>';

		return $searchXml;
	}


	function apiRunReport($email){
		/**
		 *  POST /2013/01/contactmanagement.asmx HTTP/1.1
			Host: ws.campaigner.com
			Content-Type: text/xml; charset=utf-8
			Content-Length: length
			SOAPAction: "https://ws.campaigner.com/2013/01/RunReport"

		 */
		$searchXml=$this->fetchSearchXml($email);

		$soapXml='<?xml version="1.0" encoding="utf-8"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
					  <soap:Body>
					    <RunReport xmlns="https://ws.campaigner.com/2013/01">
					        '.$this->getAuthenticationNode().'
					        '.$searchXml.'
					    </RunReport>
					  </soap:Body>
					</soap:Envelope>';

		$headerArr   = $this->buildApiHeaders($soapXml);
		//  $headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/XmlContactQuery\"";
		//  $headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/GetContacts\"";

		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/RunReport\"";
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);
	}


	function apiListAttributesOLD($email){
		/**
		 *      This gets results from contact list based on contact's email address
		 *
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1 Host: ws.campaigner.com Content-Type: text/xml;
		 *      charset=utf-8 Content-Length: length SOAPAction: "https://ws.campaigner.com/2013/01/ListAttributes"
		 *
		 *
		 *
		 */

		// staticattributeid=3  means EMAIL
		$soapXml='<?xml version="1.0" encoding="utf-8"?>
				 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				   <soap:Body>
				     <ListAttributes xmlns="https://ws.campaigner.com/2013/01">
					    ' .$this->getAuthenticationNode(). '

					</ListAttributes>
				  </soap:Body>
				</soap:Envelope>';

		$headerArr   = $this->buildApiHeaders($soapXml);
		//  $headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/XmlContactQuery\"";
		//  $headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/GetContacts\"";

		$headerArr[] = "SOAPAction: \"https://ws.campaigner.com/2013/01/ListAttributes\"";
		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

		// Sample:
		/**
		 *  https://ws.campaigner.com/2013/01/ContactsSearchCriteria2.xsd
		 *
		 *  <?xml version="1.0" encoding="utf-8"?>
			<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			<soap:Body> ...  </soap:Body>
			</soap:Envelope>
		 */
		$xml='<contactssearchcriteria>
				<version major="2" minor="0" build="0" revision="0" />
				<set>Partial</set>
				<evaluatedefault>True</evaluatedefault>
				<group>
					<filter>
						<filtertype>SearchAttributeValue</filtertype>
						<staticattributeid>1</staticattributeid>
						<action>
							<type>Text</type>
							<operator>Containing</operator>
							<value>alex</value>
						</action>
					</filter>
					<filter>
						<relation>And</relation>
						<filtertype>SearchAttributeValue</filtertype>
						<staticattributeid>3</staticattributeid>
						<action>
							<type>Text</type>
							<operator>Containing</operator>
							<value>@campaigner.com</value>
						</action>
					</filter>
				</group>
			</contactssearchcriteria>';



	}



	function apiImmediateUploadORIG($contact)
	{
		/*
		 *      POST /2013/01/contactmanagement.asmx HTTP/1.1
				Host: ws.campaigner.com
				Content-Type: text/xml; charset=utf-8
				Content-Length: length
				SOAPAction: "https://ws.campaigner.com/2013/01/ImmediateUpload"
		 */
		// Required:  "addToGroup" which will loop through the COMMA delimited numbers and iterate as "<int>XXX</int><int>..."

		// Integers of GROUP ID -- requires at least one valid list ID
		//  $contact=$contact[0];
		$addToGroupArr = @$contact['addToGroupArr'];

		if (count($addToGroupArr)<1) {
			echo "<p>No group provided, skipping this contact record. (".print_r($contact,true)."</p>";
			return false;
		}

		$addToGroupXmlArr = array();
		foreach ($addToGroupArr as $groupId) {
			$addToGroupXmlArr[] = "<int>$groupId</int>";
		}

		$addToGroupXml = implode(" ", $addToGroupXmlArr);

		//TODO: Convert this to XML Parser or use SOAP object?  Good luck conforming it right.
		// SOAP 1.1, managed to get a response, unlike SOAP 1.2
		$soapXml = '<?xml version="1.0" encoding="utf-8"?>
					  <soap:Body>
					    <ImmediateUpload xmlns="https://ws.campaigner.com/2013/01">
					    ' .
					$this->getAuthenticationNode()
					. '
						   <UpdateExistingContacts>true</UpdateExistingContacts>
						   <TriggerWorkflow>false</TriggerWorkflow>
					       <contacts>
						        <ContactData>
						          <ContactKey>
						             <ContactUniqueIdentifier>Email</ContactUniqueIdentifier>
						          </ContactKey>
						          <EmailAddress>' . $contact['emailAddress'] . '</EmailAddress>
						          <FirstName>' . $contact['firstName'] . '</FirstName>
						          <LastName>' . $contact['lastName'] . '</LastName>
						          <Status>Subscribed</Status>
						          <MailFormat>HTML</MailFormat>
						          <IsTestContact>false</IsTestContact>
						          <AddToGroup>' . $addToGroupXml . '</AddToGroup>
						        </ContactData>
					       </contacts>
					    </ImmediateUpload>
					  </soap:Body>';

		// inside ContactKey:   <ContactId>' . $contact['emailAddress'] . '</ContactId>
		//  $strip=array("\r","\n","\t");   $soapXml=str_ireplace($strip,'',$soapXml);

		/*
		 *   <PhoneNumber IsNull="true" />
		 *
		 *   <CustomAttributes>
		            <CustomAttribute xsi:nil="true" />
		            <CustomAttribute xsi:nil="true" />
		          </CustomAttributes>
		 */

		$headerArr   = $this->buildApiHeaders($soapXml);

		return $this->apiCurlRequest('POST', 'https://ws.campaigner.com/2013/01/contactmanagement.asmx', $headerArr, $soapXml);

	}


	function buildGroupNode($addToGroupArr){

		if (count($addToGroupArr)<1) {
			echo "<p>No group provided, skipping this contact record. (".print_r($addToGroupArr,true).")</p>";
			return false;
		}

		$addToGroupXmlArr = array();
		foreach ($addToGroupArr as $groupId) {
			$addToGroupXmlArr[] = "<int>$groupId</int>";
		}

		$addToGroupXml = implode(" ", $addToGroupXmlArr);

		return $addToGroupXml;
	}


	function parseSoapResponse($response){

		$resArr=explode("<ContactId>",$response);
		$nextNodeArr=explode("</ContactId>",$resArr[1]);

		$id=$nextNodeArr[0];

		//  $id=preg_match("/^(<ContactId>)\d(</ContactId>)$/",$response);

		return $id;

		//  $xml=simplexml_load_string($response) or die("Error: Cannot create SOAP object");
		//  print_r($xml);

		/** Doesn't work:
		$xml=new SimpleXMLElement($response);
		*/
		/*$response=str_ireplace('<?xml version="1.0" encoding="utf-8"?>','',$response);
		*/
		//  $response=str_ireplace("'",'"',$response);
		//  $response=utf8_decode($response);


		// Extract number between <ContactId>1791500234</ContactId>
		// Doesn't work either
		//  $matches = preg_match("/<ContactId.*>\d<\\/ContactId>/", $response);
		//  var_dump($matches);

		/*
		$xml = simplexml_load_string($response);
		print_r($xml);

		return var_dump($xml->xpath('ContactId')    );
		/*
		$blocks  = $xml->xpath('//block'); //gets all <block/> tags
		$blocks2 = $xml->xpath('//layout/block'); //gets all <block/> which parent are   <layout/>  tags
		var_dump($xml);
		//  return $xml->ContactId;
		*/

	}


}
/**  Process the form field and send to Campaigner
 *
 *   <CustomAttributes>
<CustomAttribute xsi:nil="true" />
<CustomAttribute xsi:nil="true" />
</CustomAttributes>
 */
