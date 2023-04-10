<?php

//Add all constant messages
function mrv_const_messages()
{
    $messages = "";

    $messages = array(
        'metamask_wallet' => __(" MetaMask Wallet", "mrv"),
        // 'trust_wallet' => __("Trust Wallet", "mrv"),
        'binance_wallet' => __("Binance Wallet", "mrv"),
        'wallet_connect' => __(" Wallet Connect", "mrv"),
        'click_here' => __("Click Here", "mrv"),
        'extention_not_detected' => __("extention not detected", "mrv"),
        'connection_establish' => __("Please wait while connecting...", "mrv"),
        'user_rejected_the_request' => __("User rejected the request", "mrv"),
        "infura_msg" => __("Infura project id is required for WalletConnect to work", "mrv"),

    );
    return $messages;

}

function mrv_save_votes()
{

    $nonce = !empty($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'mrv_votes_ranker')) {
        die('*ok*');
    }
    $signature = !empty($_REQUEST['signature']) ? sanitize_text_field($_REQUEST['signature']) : '';
    $sender_account = !empty($_REQUEST['sender_account']) ? sanitize_text_field($_REQUEST['sender_account']) : '';
    $vote_type = !empty($_REQUEST['vote_type']) ? sanitize_text_field($_REQUEST['vote_type']) : '';
    $ListID = !empty($_REQUEST['ListID']) ? sanitize_text_field($_REQUEST['ListID']) : '';
    $ItemName = !empty($_REQUEST['ItemName']) ? sanitize_text_field($_REQUEST['ItemName']) : '';
    $wallet_name= !empty($_REQUEST['wallet_name']) ? sanitize_text_field($_REQUEST['wallet_name']) : '';
    $current_url= !empty($_REQUEST['current_url']) ? sanitize_text_field($_REQUEST['current_url']) : '';
    $vote_res = mrv_vote($sender_account, $vote_type, $ListID, $ItemName,$wallet_name,$current_url);
    $all_list_ids = mrv_get_list_item_ids($ListID);
    $response = array(
        'status' => 'success',
        'data' => array('votes' => $vote_res, 'id' => $all_list_ids),
    );
    wp_send_json($response);

}
// Check user already voted
function mrv_check_voted_alredy()
{

    $nonce = !empty($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'mrv_votes_ranker')) {
        die('*ok*');
    }
    $db = new MRV_Database();

    $ListID = !empty($_REQUEST['ListID']) ? sanitize_text_field($_REQUEST['ListID']) : '';
    $ItemName = !empty($_REQUEST['ItemName']) ? sanitize_text_field($_REQUEST['ItemName']) : '';

    $sender_account = !empty($_REQUEST['sender_account']) ? sanitize_text_field($_REQUEST['sender_account']) : '';
    $vote_type = !empty($_REQUEST['vote_type']) ? sanitize_text_field($_REQUEST['vote_type']) : '';

    $ip = mrv_get_ip_address();
    // $user_id = get_post_meta($ListID, 'mrv_voter_id_' . $ListID.$ip, true);
    $user_id = $db->check_alredy_voted_list($ListID, $sender_account);

    if (empty($user_id)) {
        $response = array(
            'status' => 'success',
            'data' => array('updated' => 'false', 'user_id' => $user_id),
        );
        wp_send_json($response);
    }
    $voted_id = strtolower('mrv_voted_' . $ListID . $sender_account);
    $vote_res = get_post_meta($ListID, $voted_id, true);
    //var_dump($vote_res);
    $updated_votes = "";
    if (!empty($vote_res)) {
        $old_vote_type = !empty($vote_res['type']) ? $vote_res['type'] : "";
        if ($vote_type == "upvote") {
            if ($old_vote_type == "upvote") {
                $old_item = $vote_res['item_name'];
                $old_total_vote = get_post_meta($ListID, 'mrv_total_votes' . $old_item, true);
                $old_up_vote = get_post_meta($ListID, 'mrv_up_votes' . $old_item, true);
                $old_up_vote = (int) $old_up_vote - 1;
                $old_total_vote = (int) $old_total_vote - 1;
                //$down_vote = get_post_meta($ListID, 'mrv_down_votes' . $old_item, true);
                update_post_meta($ListID, 'mrv_up_votes' . $old_item, $old_up_vote);
                update_post_meta($ListID, 'mrv_total_votes' . $old_item, $old_total_vote);

            } else {
                $old_item = $vote_res['item_name'];
                $old_total_vote = get_post_meta($ListID, 'mrv_total_votes' . $old_item, true);
                $old_up_vote = get_post_meta($ListID, 'mrv_up_votes' . $old_item, true);
                $old_up_vote = (int) $old_up_vote + 1;
                $old_total_vote = (int) $old_total_vote + 1;
                //$down_vote = get_post_meta($ListID, 'mrv_down_votes' . $old_item, true);
                update_post_meta($ListID, 'mrv_up_votes' . $old_item, $old_up_vote);
                update_post_meta($ListID, 'mrv_total_votes' . $old_item, $old_total_vote);

            }
            $total_vote = get_post_meta($ListID, 'mrv_total_votes' . $ItemName, true);
            $up_vote = get_post_meta($ListID, 'mrv_up_votes' . $ItemName, true);
            $up_vote = (int) $up_vote + 1;
            $total_vote = (int) $total_vote + 1;
            update_post_meta($ListID, 'mrv_up_votes' . $ItemName, $up_vote);
            update_post_meta($ListID, 'mrv_total_votes' . $ItemName, $total_vote);
            $user_vote = array('type' => $vote_type, 'item_name' => $ItemName);
            update_post_meta($ListID, $voted_id, $user_vote);
            $updated_votes = $total_vote;

        } else {
            if ($old_vote_type == "upvote") {
                $old_item = $vote_res['item_name'];
                $old_total_vote = get_post_meta($ListID, 'mrv_total_votes' . $old_item, true);
                $old_up_vote = get_post_meta($ListID, 'mrv_up_votes' . $old_item, true);
                $old_up_vote = (int) $old_up_vote - 1;
                $old_total_vote = (int) $old_total_vote - 1;
                //$down_vote = get_post_meta($ListID, 'mrv_down_votes' . $old_item, true);
                update_post_meta($ListID, 'mrv_up_votes' . $old_item, $old_up_vote);
                update_post_meta($ListID, 'mrv_total_votes' . $old_item, $old_total_vote);

            } else {
                $old_item = $vote_res['item_name'];
                $old_total_vote = get_post_meta($ListID, 'mrv_total_votes' . $old_item, true);
                $old_up_vote = get_post_meta($ListID, 'mrv_up_votes' . $old_item, true);
                $old_up_vote = (int) $old_up_vote + 1;
                $old_total_vote = (int) $old_total_vote + 1;
                //$down_vote = get_post_meta($ListID, 'mrv_down_votes' . $old_item, true);
                update_post_meta($ListID, 'mrv_up_votes' . $old_item, $old_up_vote);
                update_post_meta($ListID, 'mrv_total_votes' . $old_item, $old_total_vote);

            }
            $total_vote = get_post_meta($ListID, 'mrv_total_votes' . $ItemName, true);
            $down_vote = get_post_meta($ListID, 'mrv_down_votes' . $ItemName, true);

            $down_vote = (int) $down_vote - 1;
            $total_vote = (int) $total_vote - 1;
            update_post_meta($ListID, 'mrv_down_votes' . $ItemName, $down_vote);
            update_post_meta($ListID, 'mrv_total_votes' . $ItemName, $total_vote);
            $user_vote = array('type' => $vote_type, 'item_name' => $ItemName);
            update_post_meta($ListID, $voted_id, $user_vote);
            $updated_votes = $total_vote;

        }
        $all_list_ids = mrv_get_list_item_ids($ListID);

        $response = array(
            'status' => 'success',
            'data' => array('updated' => 'updated', 'votes' => $updated_votes, 'id' => $all_list_ids),
        );
        wp_send_json($response);

    }
    $response = array(
        'status' => 'error',
        'data' => 'error',
    );
    wp_send_json($response);

}

// Vote function
function mrv_vote($sender_account, $vote_type, $ListID, $ItemName,$wallet_name,$current_url)
{
    $result = [];
    $db = new MRV_Database();

    $result['post_id'] = $ListID;
    $result['user_id'] = get_current_user_id();
    $ip = mrv_get_ip_address();
    $voted_id = strtolower('mrv_voted_' . $ListID . $sender_account);
    //$user_id = get_post_meta($ListID, 'mrv_voter_id_' . $ListID.$ip, true);
    $user_id = $db->check_alredy_voted_list($ListID, $sender_account);
    if (!empty($user_id)) {
        return 'updated';
    }

    //update_post_meta($ListID, 'mrv_voter_id_' . $result['post_id'].$ip, $sender_account);
    // update_post_meta($ListID, 'mrv_voted_'.$ip.$result['post_id'].$sender_account, 'voted');

    $total_vote = get_post_meta($result['post_id'], 'mrv_total_votes' . $ItemName, true);
    $up_vote = get_post_meta($result['post_id'], 'mrv_up_votes' . $ItemName, true);
    $down_vote = get_post_meta($result['post_id'], 'mrv_down_votes' . $ItemName, true);

    // $db->create_table();

    $logs = array();
    $logs['list_name'] = get_the_title($ListID);
    $logs['list_item'] = $ItemName;
    $logs['list_id'] = $ListID;
    $logs['ip_address '] = $ip;
    $logs['user_wallet'] = $sender_account;
    $logs['vote_type'] = $vote_type;
    $logs['wallet_name'] = $wallet_name;
    $logs['article_url'] = $current_url;
    $logs['user_agent'] = mrv_get_the_browser();

   

    if ($vote_type == "upvote") {
        $up_vote = (int) $up_vote + 1;
        $total_vote = (int) $total_vote + 1;
        update_post_meta($result['post_id'], 'mrv_up_votes' . $ItemName, $up_vote);
        update_post_meta($result['post_id'], 'mrv_total_votes' . $ItemName, $total_vote);
        $user_vote = array('type' => $vote_type, 'item_name' => $ItemName);
        update_post_meta($ListID, $voted_id, $user_vote);

        $logs['up_vote'] = $up_vote;
        $logs['down_vote'] = $down_vote;
        $logs['total_vote'] = $total_vote;
        $db->insert($logs);
        return $total_vote;
    } else {
        $down_vote = (int) $down_vote - 1;
        $total_vote = (int) $total_vote - 1;
        update_post_meta($result['post_id'], 'mrv_down_votes' . $ItemName, $down_vote);
        update_post_meta($result['post_id'], 'mrv_total_votes' . $ItemName, $total_vote);
        $user_vote = array('type' => $vote_type, 'item_name' => $ItemName);
        update_post_meta($ListID, $voted_id, $user_vote);
        $logs['up_vote'] = $up_vote;
        $logs['down_vote'] = $down_vote;
        $logs['total_vote'] = $total_vote;
        $db->insert($logs);
        return $total_vote;
    }

}

function mrv_get_ip_address()
{
    // Equally untrustworthy.
    $ip = "";
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

        //check ip from share internet

        $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);

    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        //to check ip is pass from proxy

        $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);

    } else {

        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);

    }
    return $ip;

}

/**
 * Create HTML for item media ( image / youtube / slider )
 */
function mrv_item_media($item)
{

    $html = null;
    if (!empty($item['item-media-type']) && $item['item-media-type'] == 'image' && !empty($item['item-media-image']['url'])) {
        $alt_text = !empty($item['item-media-image-alt']) ? $item['item-media-image-alt'] : $item['item-title'];
        $image_size = !empty($item['item-image-size']) ? $item['item-image-size'] : 'large';
        $image_size_class = $image_size;
        if ($image_size == 'custom_size') {
            $width = $item['item-image-custom-size']['width'];
            $height = $item['item-image-custom-size']['height'];
            $image_size = array($width, $height);
            $image_size_class = 'custom';
        }
        $html .= '<div class="mrv-media ' . esc_attr($image_size_class) . '">';

        $html .= wp_get_attachment_image($item['item-media-image']['id'], $image_size, false, array('class' => 'mrv-media ' . esc_attr($image_size_class), 'alt' => $alt_text));

        $html .= '</div>';
    } elseif ($item['item-media-type'] == 'youtube' && !empty($item['item-media-youtube'])) {

        $video = $item['item-media-youtube'];
        // convert youtube video link for iframe
        if (strpos($video, 'youtube') > 0 || strpos($video, 'youtu.be') > 0) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video, $matches);

            if (isset($matches[1])) {
                $id = $matches[1];
                $html = '<div class="mrv-media"><iframe width="100%"
					src="https://www.youtube.com/embed/' . $id . '"
					frameborder="0" allowfullscreen></iframe></div>';
            }
        } else {
            $html = __("Wrong URL", "twae");
        }

    }

    return $html;
}

function mrv_get_list_item_ids($listID)
{
    $options = get_post_meta($listID, 'mrv_post_settinga', true);
    $items = !empty($options['mrv-item']) ? $options['mrv-item'] : '';
    $ids = [];
    if (is_array($items)) {
        foreach ($items as $key => $value) {

            $itemtitle = $value['item-multiple-settings']['item-title'];
            $uniq_id = mrv_clean($itemtitle);
            $ids[] = array('ids' => $uniq_id . $listID, 'votes' => get_post_meta($listID, 'mrv_total_votes' . $uniq_id, true));
        }
    }

    return $ids;
}

function mrv_clean($string)
{
    $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}
//About started page callback function

function mrv_about_page()
{
    ?>
<p>Create polls, make any list in your posts votable - images, videos, whole paragraphs, even bullet points!.
Make your content more interactive and maximize user engagement and retention.
Restrict misuse using Crypto Wallet Connection</p>

<div class="csf-welcome-cols">


 <div class="csf--col csf--col-first">
    <span class="mrv-image-wrap"><img src="<?php echo esc_url(MRV_URL . 'assets/images/meta-auth.jpg') ?>"></span>
    <div class="mrv-wrap-cont">
    <div class="mrv-title">Meta Auth</div>
    <p class="mrv-desc">2-Factor authentication is a tedious process that can be simplified using the functionality of signed messages with a private key with an overall flow …</p>
</div>
<button class="button button-secondary cpmwp_modal-toggle"><a href="<?php echo esc_url(admin_url() . 'plugin-install.php?tab=plugin-information&amp;plugin=meta-auth'); ?>" targer="_blank">Install Plugin</a></button>

  </div>
  <div class="csf--col csf--col-first">
    <span class="mrv-image-wrap"><img src="<?php echo esc_url(MRV_URL . 'assets/images/meta-age.jpg') ?>"></span>
      <div class="mrv-wrap-cont">
    <div class="mrv-title">Meta Age</div>
    <p class="mrv-desc">A better way to restrict crypto users by their age with blockchain and browser wallets.</p>
</div>
<button class="button button-secondary cpmwp_modal-toggle"><a href="<?php echo esc_url(admin_url() . 'plugin-install.php?tab=plugin-information&amp;plugin=meta-age'); ?>" targer="_blank">Install Plugin</a></button>

  </div>

  <div class="csf--col csf--col-first csf--last">
    <span class="mrv-image-wrap"><img src="<?php echo esc_url(MRV_URL . 'assets/images/meta-locker.jpg') ?>"></span>
      <div class="mrv-wrap-cont">
    <div class="mrv-title">Meta locker</a></div>
    <p class="mrv-desc">A content locker WordPress plugin. Users are required to connect their crypto wallets to view the locked content.</p>
</div>

<button class="button button-secondary cpmwp_modal-toggle"><a href="<?php echo esc_url(admin_url() . 'plugin-install.php?tab=plugin-information&amp;plugin=meta-locker'); ?>" targer="_blank">Install Plugin</a></button>

  </div>

  <div class="clear"></div>



  <div class="clear"></div>




  <div class="clear"></div>

</div>

<hr />
   <?php
}
//Getting  started page callback function
function mrv_getting_start()
{
    ?>
  <p><strong>Lets quick start it.</strong></p>
<p>In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. Lorem ipsum may be used as a placeholder before final copy is available.</p>

<?php
}
//Terms page callback function
function mrv_terms_of_use()
{
    ?>
        <div class="wrap metaranker-tou-page">
			<h1 style="font-size:24px;text-transform:uppercase;font-weight:700"><?= __('Terms of Use', 'meta-ranker'); ?></h1>
			<p>This page describes Digital Governance Services’s practices regarding the collection, use and disclosure of the information we collect from and about you when you use our Service. We take our obligations regarding your privacy seriously and have made every effort to draft this Privacy Policy in a manner that is clear and easy for you to understand.</p>
			<p>This Privacy Policy (“<strong>Policy</strong>”) describes how we collect, process, share and safeguard Personal Data we collect from you, or that you provide to us, in connection with the provision of any services through this Website or elsewhere (“<strong>Websites</strong>”), to provide web-based tools that enable our customers to use data to run their businesses more effectively (“<strong>Services</strong>”). We gather Personal Data that we collect about investors in an online database, that is made available to users of our Services and to our customers. This Policy also tells you about your rights and choices with respect to your Personal Data, and how you can contact us if you have any questions or concerns. In this Policy, “Personal Data“ means any information relating to an identified or identifiable individual. Please read this Policy carefully.</p>
			<p>If you do not agree with this Privacy Policy or any part thereof, you should not access or use any part of the Services. If you change your mind in the future, you must stop using the Services and you may exercise your rights in relation to your Personal Data as set out in this Policy.</p>
			<p>If we hold data about you it's because of one or more of the following:</p>
			<ol>
				<li>You've created a free account on one of our Websites.</li>
				<li>Recordings of your personal data have been shared with us from data partners who have direct contact with you already, and where appropriate notice has been given for them to pass your information to us.</li>
				<li>We are recording personal data from public sources, including the government, social media websites, and other web properties where your data is publicly available.</li>
			</ol>

			<h2>Personal Data We Collect</h2>
			<p>We obtain Personal Data about you from various sources described below.</p>
			<h3>Personal Data You Provide to Us:</h3>
			<ol>
				<li><strong>Account Information</strong>. If you create an account to use our Services, we collect Personal Data to allow you to use our Services via this account. When you sign up, you provide us with your name, email address, password, wallet address and other account information necessary to create and maintain your account.</li>
				<li><strong>Contact information and other information you choose to provide to Us</strong>. You have the ability to provide a variety of information during your interactions with us, such as through emails or other communications. When you contact us via a contact form, email, or other means, you provide us with Personal Data, such as your name and contact details, and the content of your communications.</li>
				<li><strong>Financial Information</strong>. If you purchase any services through our Websites, you will need to provide us with your credit card information. We will use that information solely for the purpose of fulfilling your purchase request. We ourselves do not store any credit card information, but rely on the services of payment providers to carry out any payments you wish to make to us.</li>
			</ol>
			<p>Where applicable, we may indicate whether and why you must provide us with your Personal Data as well as the consequences of failing to do so. For example, it may be necessary for you to disclose certain Personal Data in order for us to provide the Services to you.</p>
			<h3>Information Collected via Automated Means</h3>
			<ol>
				<li><strong>Single Sign-On Through Internet Service Providers</strong>. We may collect Personal Data from internet service providers when you decide to use their single sign-in servers to connect to our Services. Your interactions with these tools are governed by the privacy policies of the corresponding social media platforms.</li>
				<li><strong>Cookies and Similar Technologies</strong>. e collect Personal Data via cookies, pixel tags, or similar technologies on our Services (collectively referred to as “<strong>cookies</strong>”). For more information on our use of cookies, please read our <a href="https://rocketreach.co/cookies" target="_blank">Cookie Policy</a>.</li>
				<li><strong>Device and Usage Information</strong>. When you access and use the Services, we receive and store information about interactions with our Services, such as date/time stamps, usage information and statistics, as well as your device, including your internet protocol (IP) address, browser type, device type, device identifiers, operating system version and type.</li>
			</ol>
			<h3>Information We Receive from Third Parties</h3>
			<ol>
				<li><strong>Publicly Available Information</strong>. Our search technology scans the web and records publicly available information from third party websites, such as, social media sites, corporate websites and public records. This information may include your name, email address, phone, work history, education, location, social profile, wallet address, profile photos, and other information. Our technology may also infer Personal Data about you by making assumptions based on publicly available information that may not directly concern you, combined with other information we have about you.</li>
				<li><strong>Other Third Parties</strong>. We may obtain Personal Data about you from third parties such as people directories, data brokers, corporate information directories and other entities. This information may include name, email, phone, work history, education, location, social profile, skills and other similar information.</li>
			</ol>
			<h2>Legal Bases for the Processing of Personal Data</h2>
			<p>Digital Governance Services endeavours to be compliant with all the provisions of the GDPR as to any Personal Data in its posession.</p>
			<p>We only use your Personal Data as described in this section if we have a valid legal ground for the processing, including:</p>
			<ol>
				<li><strong>Consent</strong>. This is the case where you have consented to the use of your Personal Data.</li>
				<li><strong>Contract</strong>. We need your Personal Data to provide you with our Services in order to perform our end of our contracts with you, such as to create and secure your account, or to respond to your inquiries.</li>
				<li><strong>Legitimate Interest</strong>. We have a legitimate business interest in processing your Personal Data to provide the Services to our Customers. We only rely on legitimate interest as a legal basis when such legitimate interests are not overridden by your interests or your fundamental rights and freedoms and we ensure we comply with any request you make to exercise your rights.</li>
				<li><strong>Legal Obligation</strong>. We may have a legal obligation to process your Personal Data, for example to comply with tax and accounting obligations, and we may process your Personal Data when necessary to establish, exercise, or defend legal claims. We may also process your Personal.</li>
			</ol>
			<h2>Who We Share Personal Data With</h2>
			<p>Digital Governance Services endeavors to be compliant with GDPR We may disclose Personal Data about you in the following circumstances:</p>
			<ol>
				<li><strong>Customers and Users of the Website</strong>. We make available Personal Data such as your name, email, phone, work history, education, location, skillset, social profiles, wallet address, and related data to our customers, and to users of our Website, in order to provide them with the Services.</li>
				<li><strong>Service Providers</strong>. We work with third parties to provide services such as hosting, maintenance, and support. These third parties may have access to or process your Personal Data as part of providing those services to us. For example:
					<ol class="nested-list">
						<li>We use service providers such as payment providers to process credit card payments on our Website. When you use a credit card to pay for our services, information such as your name, billing address, phone number, email address and credit card information will be submitted to service providers for verification and to manage any recurring payments.</li>
						<li>Cloud service providers who we rely on for data storage, disaster recovery and to perform our obligations to you.</li>
						<li>We use providers of business communication tools.</li>
					</ol>
				</li>
				<li><strong>Legal</strong>. nformation about our users, including Personal Data, will be disclosed to law enforcement agencies, regulatory bodies, public authorities or pursuant to the exercise of legal proceedings if we are legally required to do so, or if we believe, in good faith, that such disclosure is necessary to comply with a legal obligation or request, to enforce our terms and conditions, to prevent or resolve security or technical issues, or to protect the rights, property or safety of Digital Governance Services, our users, a third party, or the public.</li>
				<li><strong>Change of Corporate Ownership</strong>. Information about our users, including Personal Data, may be disclosed and otherwise transferred to an acquirer, successor, or assignee as part of any merger, acquisition, debt financing, sale of assets, or similar transaction, as well as in the event of an insolvency, bankruptcy, or receivership in which information is transferred to one or more third parties as one of our business assets.</li>
				<li><strong>Aggregated Information</strong>. We may use and disclose aggregated or otherwise anonymized information for any purpose, unless we are prohibited from doing so under applicable law.</li>
				<li><strong>Business Partners</strong>. We may share Personal Data such as name, email, phone, work history, education, location, skillset, wallet address, social profiles and related data with our business partners, including for the purposes of sales, marketing, recruiting and other related purposes.</li>
			</ol>
			<h2>Your Choices About How We Use and Disclose Your Information</h2>
			<p>We strive to provide you with choices regarding the Personal Data you provide to us. We have created mechanisms to provide you with the following control over your information:</p>
			<ol>
				<li><strong>Promotional Materials</strong>. If you do not wish to have your email address or other contact information used by Digital Governance Services or its affiliates for marketing purposes to promote our own or our affiliates’ or subsidiaries’ products or services, you can opt out by contacting us as set out in the “<strong>Contact Us</strong>“ section below. If we have sent you a newsletter or promotional email, you may opt-out of receiving them by following the instructions included in each newsletter or communication.</li>
				<li><strong>Accessing, Deleting and Correcting Your Information</strong>. You may contact us as set out in the “<strong>Contact Us</strong>“ section below to request access to, correct or delete any Personal Data that we have about you. We cannot delete your Personal Data except by also deleting your user account. We may not accommodate a request to change information if we believe the change would violate any law or legal requirement or cause the information to be incorrect.</li>
			</ol>
			<p>We do not control third parties’ collection or use of your information to serve interest-based advertising. However, these third parties may provide you with ways to choose not to have your information collected or used in this way.</p>
			<h2>Your Privacy Rights</h2>
			<p>You have the following rights, as provided under applicable law and subject to any limitations in such law:</p>
			<ol>
				<li><strong>Right to Access</strong>. You have the right to obtain access to the Personal Data we hold about you and to request certain information about our processing. More in particular, you have the right to receive an explanation of (i) why we process your Personal Data, (ii) the categories of Personal Data we have about you, (iii) who we share your Personal Data with, (iv) how long we store your Personal Data and (v) who we received your Personal Data from, if it was not collected from you directly. We will also inform you about your privacy rights..</li>
				<li><strong>Right to Rectification</strong>. You have the right to correct, update or complete any Personal Data we hold about you that is inaccurate or incomplete. Please note that we may rectify or remove incomplete or inaccurate information, at any time and at our own discretion.</li>
				<li><strong>Right to Erasure</strong>. You may request to have your Personal Data anonymized, erased or deleted, as appropriate. In this case, if there is no overriding legitimate interest to continue processing your Personal Data we will erase your data.</li>
				<li><strong>Right to Object to Processing</strong>. You have the right to object to our processing of your Personal Data where we are relying on a legitimate interest or if we are processing your Personal Data for direct marketing purposes.</li>
				<li><strong>Right to Restrict Processing</strong>. You have a right in certain circumstances to stop us processing your Personal Data other than for storage purposes.</li>
				<li><strong>Right to Portability</strong>. You have the right to receive, in a structured, commonly used and machine-readable format, Personal Data that we hold about you, if we process it on the basis of our contract with you, or with your consent, or to request that we transfer such Personal Data to a third party.</li>
				<li><strong>Right to Withdraw Consent</strong>. You may withdraw any consent you previously provided to us regarding the processing of your Personal Data at any time and free of charge. We will apply your preferences going forward. This will not affect the lawfulness of the processing before you withdrew your consent.</li>
				<li><strong>Right to Lodge a Complaint</strong>. You may lodge a complaint with a supervisory authority, including in your country of residence, place of work, or where you believe an incident took place.</li>
			</ol>
			<p>You may exercise these rights by contacting us as set out in the “<strong>Contact Us</strong>“ section below. Please note that, prior to any response to the exercise of such rights, we will require you to verify your identity. In addition, we may have valid legal reasons to refuse your request and will inform you if that is the case. Note that applicable laws contain certain exceptions and limitations to each of these rights.</p>
			<h2>International Data Transfers</h2>
			<p>If you provide us with your Personal Data when using the Services from the EEA, Switzerland or the UK or other regions of the world with laws governing data collection and use that may differ from U.S. law, then please note that you are transferring your Personal Data outside of those regions to the United States for storage and processing.</p>
			<p>If we transfer your Personal Data internationally, we will ensure that relevant safeguards are in place to afford adequate protection for your Personal Data and we will comply with applicable data protection laws, in particular by relying on an EU Commission adequacy decision, on contractual protections for the transfer of your Personal Data or a derogation if available. For more information about how we transfer Personal Data internationally, please contact us as set out in the “<strong>Contact Us</strong>“ section below.</p>
			<h2>Children’s Privacy</h2>
			<p>Our Services are not directed to children, and we do not knowingly collect Personal Data from children under the age of 18. If you learn that a child has provided us with Personal Data in violation of this Policy, please contact us as set out in the “<strong>Contact US</strong>“ section below.</p>
			<h2>Data Security</h2>
			<p>We have implemented physical, managerial, and technical safeguards that are designed to improve the integrity of Personal Data that we collect, maintain, and otherwise process and to secure it from unauthorized access, use, alteration and disclosure. All information you provide to us is stored on our secure servers behind firewalls. Any payment transactions will be encrypted using SSL technology.</p>
			<p>The safety and security of your information also depends on you. Where we have given you (or where you have chosen) a password for access to certain parts of our Website, you are responsible for keeping this password confidential. We ask you not to share your password with anyone.</p>
			<p>The transmission of information via the internet is not completely secure. Although we do our best to protect your Personal Data, we cannot guarantee the security of your Personal Data transmitted to our Website. Any transmission of Personal Data is at your own risk.</p>
			<h2>Data Retention</h2>
			<p>We store all Personal Data for as long as necessary to fulfill the purposes set out in this Policy, or for as long as we are required to do so for backups, archiving, prevention of fraud and abuse, analytics, satisfaction of legal or regulatory obligations, or where we otherwise reasonably believe that we have a legitimate reason to do so. Retention periods will be determined taking into account the type of information that is collected and the purpose for which it was collected, bearing in mind the requirements applicable to the situation and the need to delete outdated, unused information at the earliest reasonable time.</p>
			<p>When deleting Personal Data, we will take measures to render such Personal Data irrecoverable or irreproducible, and the electronic files which contain Personal Data will be permanently deleted.</p>
			<h2>Data Breaches</h2>
			<p>We take great care to ensure the security and privacy of any Personal Data we hold. In the event of a personal data breach with a likelihood of harm, we will immediately notify all parties whose Personal data was part of the breach.</p>
			<h2>Third Party Services</h2>
			<p>The Services may contain features or links to websites and services provided by third parties. Any information you provide on third-party sites or services is provided directly to the operators of such services and is subject to those operators’ policies, if any, and governing privacy and security, even if accessed through the Services. We are not responsible for the content or privacy and security practices and policies of third-party sites or services to which links or access are provided through the Service and we encourage you to learn about third parties’ privacy and security policies before providing them with your Personal Data.</p>
			<h2>Changes and Updates to this Policy</h2>
			<p>We may update this Policy from time to time to reflect changes in our privacy practices. It is our policy to post any changes we make to this Policy online. The date the Policy was last revised is identified at the top of the page. Please monitor our Website and this Policy periodically to check for any changes. If we make material changes to how we treat our users’ Personal Data, we will notify you by email to the email address specified in your account and/or through a notice on the Website home page.</p>
			<h2>Complaints</h2>
			<p>If you wish to lodge a complaint about how we process your Personal Data, please contact us at <a href="mailto:privacy@januus.io">privacy@januus.io</a>. We will endeavor to respond to your complaint as soon as possible. You may also lodge a claim with the data protection supervisory authority in the EU country in which you live or work, or where you believe we have infringed data protection laws.</p>
			<h2>Contact Us</h2>
			<p>Digital Governance Services is the entity responsible for the processing of your Personal Data and for the purpose of the European Union’s General Data Protection Regulation, is the data controller in respect of the processing of your Personal Data. If you have any questions or comments about this Policy, our privacy practices, or if you would like to exercise your rights with respect to your Personal Data, please contact us by one of the following methods:</p>
			<p>Privacy requests: privacy@januus.io or via mail at:</p>
			<p><strong>Data Privacy Team, Digital Governance Services<br>
					International House, 6 South Molton St, London, United Kingdom, W1K 5QF</strong></p>
			<p>In addition, you may contact our data protection officer at <a href="mailto:privacy@januus.io">privacy@januus.io</a>.</p>
		</div>
<?php
}


function mrv_get_the_browser()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        return 'Internet explorer';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
        return 'Internet explorer';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false) {
        return 'Mozilla Firefox';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false) {
        return 'Google Chrome';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false) {
        return 'Opera Mini';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false) {
        return 'Opera';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false) {
        return 'Safari';
    } else {
        return 'Other';
    }

}