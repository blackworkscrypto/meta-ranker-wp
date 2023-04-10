jQuery('document').ready(function ($) {
    // console.log(wallets_data)  
    $('.mrv-vote-btn').on('click',function (e) {   
        
        var VoteType = $(this).attr('data-Vtype')
        var ListID = $(this).attr('data-ListID')
        var ItemName = $(this).attr('data-ItemName')
        let MetaMaskEnable = (wallets_data.wallets_enable.metamask_wallet != undefined && wallets_data.wallets_enable.metamask_wallet != "")?wallets_data.wallets_enable.metamask_wallet:"1"
        let BinanceEnable = (wallets_data.wallets_enable.binance_wallet != undefined && wallets_data.wallets_enable.binance_wallet  != "") ? wallets_data.wallets_enable.binance_wallet  : "1"
        let WalletCEnable = (wallets_data.wallets_enable.wallet_connect != undefined && wallets_data.wallets_enable.wallet_connect  != "") ? wallets_data.wallets_enable.wallet_connect  : "1"
        // let TrustWEnable = (wallets_data.wallets_enable.trust_wallet != undefined && wallets_data.wallets_enable.trust_wallet != "") ? wallets_data.wallets_enable.trust_wallet : "1"
       
        let pophtml = '<div class="mrv-connector-modal"><div class="mrv-modal-content" ><ul class="mrv-wallets" >'
        if (MetaMaskEnable =="1"){
        pophtml += '<li class="mrv-wallet" id = "metamask_wallet" >'+
            '<div class="mrv-wallet-icon" ><img src="' + wallets_data.url +'assets/images/images/metamask.png" alt="metamask" ></div>'+
            '<div class="mrv-wallet-title" >' + wallets_data.const_msg.metamask_wallet+'</div>'+
                            '</li >';
        }
        if (BinanceEnable == "1") {
        pophtml += '<li class="mrv-wallet" id = "Binance_wallet" >' +
            '<div class="mrv-wallet-icon" ><img src="' + wallets_data.url +'assets/images/images/binance.jpg" alt="metamask" ></div>' +
            '<div class="mrv-wallet-title" >' + wallets_data.const_msg.binance_wallet +'</div>' +
            '</li >';
        }
        if (WalletCEnable == "1") {
        pophtml += '<li class="mrv-wallet" id = "wallet_connect" >' +
            '<div class="mrv-wallet-icon" ><img src="' + wallets_data.url +'assets/images/images/walletconnect.png" alt="metamask" ></div>' +
            '<div class="mrv-wallet-title" >' + wallets_data.const_msg.wallet_connect +'</div>' +
            '</li >';
        }
        // if (TrustWEnable == "1") {
        // pophtml += '<li class="mrv-wallet" id = "trust_wallet" >' +
        //     '<div class="mrv-wallet-icon" ><img src="' + wallets_data.url + 'assets/images/images/trustwallet.png" alt="metamask" ></div>' +
        //     '<div class="mrv-wallet-title" >' + wallets_data.const_msg.trust_wallet +'</div>' +
        //     '</li >';
        // }
        pophtml += '</ul></div></div>';
        Swal.fire({
            allowOutsideClick: false,
            html: pophtml,
            customClass: { container: 'mrv_main_popup_wrap', popup: 'mrv_popup' },
            showCloseButton:true,
            showConfirmButton: false,
            didOpen: () => {
                var wallet_selector = Swal.getPopup().querySelectorAll('.mrv-wallet')
            
                jQuery(wallet_selector).click(function (evt) {
                    let current_wallet = $(this).attr('id')
                    
                   
                    mrv_wallets(current_wallet, VoteType, ListID, ItemName)
                })
          
            
            },
        })
    })
    
})







   
 
let mrv_wallets = async (wallet_id, VoteType, ListID, ItemName) =>{

            var wallet_connect = "";
            var wallet_links = ""     
            var wallet_object = "";
            let wallet_name = "";
            const EnableWconnect = mrv_get_widnow_size()
            switch (wallet_id) {
                case "metamask_wallet":
                    wallet_name = wallets_data.const_msg.metamask_wallet
                    if (EnableWconnect == true) {
                        wallet_object = await mrv_wallet_connect(wallet_name, wallet_id)
                    }
                    else {
                        wallet_object = window.ethereum
                    }

                    wallet_links = "https://chrome.google.com/webstore/detail/metamask/nkbihfbeogaeaoehlefnkodbefgpgknn"
                    break;
                // case "trust_wallet":
                //     wallet_name = wallets_data.const_msg.trust_wallet
                //     if (EnableWconnect == true) {
                //         wallet_object = await mrv_wallet_connect(wallet_name, wallet_id)
                //     }
                //     else {
                //         wallet_object = window.trustwallet
                //     }

                //     wallet_links = "https://chrome.google.com/webstore/detail/trust-wallet/egjidjbpglichdcondbcbdnbeeppgdph"
                //     break;
                case "Binance_wallet":
                    wallet_name = wallets_data.const_msg.binance_wallet
                    if (EnableWconnect == true) {
                        wallet_object = await mrv_wallet_connect(wallet_name, wallet_id)
                    }
                    else {
                        wallet_object = window.BinanceChain
                    }

                    wallet_links = "https://chrome.google.com/webstore/detail/binance-wallet/fhbohimaelbohpjbbldcngcnapndodjp"
                    break;
                case "wallet_connect":
                    wallet_name = wallets_data.const_msg.wallet_connect
                    wallet_object = await mrv_wallet_connect(wallet_name, wallet_id)
                    wallet_links = ""
                    break;
            }
    
            if ((wallet_id == "wallet_connect" && (wallets_data.infura_id == undefined || wallets_data.infura_id == "")) || (EnableWconnect == true && (wallets_data.infura_id == undefined || wallets_data.infura_id == ""))) {
                mrv_alert_msg(wallets_data.const_msg.infura_msg, "warning", false)
            }
            else if (typeof wallet_object === 'undefined' || wallet_object == "") {
                const el = document.createElement('div')
                el.innerHTML = '<a href="' + wallet_links + '" target="_blank">Click Here </a> to install ' + wallet_name + ' extention'

                Swal.fire({
                    title: wallet_name + wallets_data.const_msg.extention_not_detected,
                    customClass: { container: 'mrv_main_popup_wrap', popup: 'mrv_popup' },
                    html: el,
                    icon: "warning",
                })

            }
            else {

                const provider = new ethers.providers.Web3Provider(wallet_object, "any");
                const network = await provider.getNetwork()
                let accounts = await provider.listAccounts();
                if (accounts.length == 0) {

                    Swal.fire({
                        text: wallets_data.const_msg.connection_establish,
                        customClass: { container: 'mrv_main_popup_wrap', popup: 'mrv_popup' },
                        didOpen: () => {
                            Swal.showLoading()
                        },

                        allowOutsideClick: false,
                    })
                    await provider.send("eth_requestAccounts", []).then(function (account_list) {
                        // console.log(account_list)
                        accounts = account_list
                        Swal.close()
                    }).catch((err) => {
                        // console.log(err)
                        mrv_alert_msg(wallets_data.const_msg.user_rejected_the_request, 'error', 2000)

                    })

                }
                if (accounts.length) {
                    const account = accounts[0]
                      var request_data = {
                        'action': 'mrv_check_voted_alredy',
                        'nonce': wallets_data.nonce,
                        'sender_account': account,
                        'ListID': ListID,
                        'vote_type': VoteType,
                        'ItemName': ItemName,
                    };
                    jQuery.ajax({
                        type: "post",
                        dataType: "json",
                        url: wallets_data.ajax,
                        data: request_data,
                        success: function (data) {                       
                            if(data.status=="success"){
                                if (data.data.updated =="updated"){
                                    let vote = data.data.votes
                                    let id = data.data.id

                                    jQuery.each(id, function (key, val) {
                                        
                                        let votess = (val.votes == "0") ? '' : val.votes
                                        jQuery('#mrv_total_votes_' + val.ids).html((votess > 0) ? '+'+votess : votess)
                                    })
                                    mrv_alert_msg("Vote Updated Successfully", 'success', false)
                                    setTimeout(function() {
                                        location.reload();
                                     }, 1000);
                                }                               
                                else{
                                    if (data.data.user_id != undefined && data.data.user_id == ""){
                                        MrvExtentionCall(account, provider, wallet_id, VoteType, ListID, ItemName, wallet_name)
                                    }
                                }
                            }
                            else if (data.status == "error") {
                                mrv_alert_msg("Something Went Wrong Please Try Again", 'error', false)
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            console.log("Status: " + textStatus + "Error: " + errorThrown);
                        }
                    })  

                   // MrvExtentionCall(account, provider, wallet_id, VoteType, ListID, ItemName)
                }
            }

        }







function MrvExtentionCall(account, provider, wallet_id, VoteType, ListID, ItemName, wallet_name) {

        try {
            const signer = provider.getSigner()
            let msg='Sign this request'
            let messageHash = ethers.utils.id(msg);
            let messageHashBytes = ethers.utils.arrayify(messageHash)
            let EnableWconnect = mrv_get_widnow_size()
            let vote_msg = (wallet_id == "wallet_connect" || EnableWconnect == true || wallet_id == "Binance_wallet") ? messageHashBytes : msg
            const trans = signer.signMessage(vote_msg).then(async function (res) {
                // console.log(res);

                var request_data = {
                    'action': 'mrv_save_votes',
                    'nonce': wallets_data.nonce,
                    'signature': res,
                    'sender_account': account,
                    'vote_type': VoteType,
                    'wallet_name': wallet_name,
                    'current_url': wallets_data.current_url,
                    'ListID': ListID,
                    'ItemName': ItemName,
                };
                jQuery.ajax({
                    type: "post",
                    dataType: "json",
                    url: wallets_data.ajax,
                    data: request_data,
                    success: function (data) {
                        //  console.log(data)
                        if (data.status == "success") {
                            if (data.data.votes=='updated'){
                                mrv_alert_msg("Voted Already", 'success', false)
                            }
                            else{
                            let vote=data.data.votes
                            let id = data.data.id
                      
                                jQuery.each(id,function (key,val) {
                                    let votess = (val.votes == "0") ? '' : val.votes
                                    jQuery('#mrv_total_votes_' + val.ids).html((votess > 0) ? '+'+votess : votess)
                                })
                          
                        mrv_alert_msg("Voted Successfully", 'success', false)

                        setTimeout(function() {
                            location.reload();
                         }, 100);
                            }
                            
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown) {
                        console.log("Status: " + textStatus + "Error: " + errorThrown);
                    }
                })


                
            }).catch(function (error) {
                // console.log(error)
                if (error.code == "4001") {
                    mrv_alert_msg(error.message, 'error', 2000)
                    return;
                }
                else if (error.code == "-32602") {
                    mrv_alert_msg(error.message, 'error', 10000)
                    return;
                }
                else {
                    mrv_alert_msg(error.message, 'error', false)
                    return;
                }
             
            });
        }
        catch (erro) {
            console.log(erro)
         
        }
    

}

function mrv_ajx_handler(params) {
    
}


function mrv_get_widnow_size() {
    if (window.innerWidth <= 500) {
        return true;
    }
    else {
        return false;
    }
}
async function mrv_wallet_connect(wallet_name, id) {

    if (wallets_data.infura_id == undefined || wallets_data.infura_id == "" ) {
        return
    }
    let walletConnect = new WalletConnectProvider.default({
        infuraId: wallets_data.infura_id,
        rpc: wallets_data.rpc_urls,
    });
    walletConnect.on('connect', (error) => {
        console.log(error)
    });
    walletConnect.on('disconnect', (error) => {
        console.log(error)
    });
    setTimeout(() => {
        if (id != "wallet_connect") {
            let header = jQuery('#walletconnect-wrapper .walletconnect-modal__header')
            header.find('img').attr("src", wallets_data.wallet_logos[id])
            header.find('p').html(wallet_name)
        }
        /* if (mrv_get_widnow_size()==false){
        jQuery('#walletconnect-wrapper #walletconnect-qrcode-text').html('Scan QR code with ' + wallet_name +'')
        } */
        jQuery('#walletconnect-wrapper').click(function (params) {
            if (id != "wallet_connect") {
                let header = jQuery('#walletconnect-wrapper .walletconnect-modal__header')
                header.find('img').attr("src", wallets_data.wallet_logos[id])
                header.find('p').html(wallet_name)
            }
        })
    }, 250);
    setTimeout(() => {
        jQuery('#walletconnect-wrapper svg.walletconnect-qrcode__image').css({ 'width': '60%' })
    }, 50);

    await walletConnect.enable();
    return walletConnect;



}
function cdbbc_ajax_handler(request_data) {

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: wallets_data.ajax,
        data: request_data,
        success: function (data) {
            if (data.status == "success") {
                return true;
            } else {
                return false;
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("Status: " + textStatus + "Error: " + errorThrown);
        }

    });

}

function mrv_alert_msg(msg, icons = false, time) {
    Swal.close()
    Swal.fire({
        title: msg,     
        icon: icons,
        timer: time,

    })

}