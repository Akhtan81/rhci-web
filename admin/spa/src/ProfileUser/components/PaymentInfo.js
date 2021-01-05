import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';

import selectors from './selectors';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {MODEL_CHANGED} from "../actions";

class PaymentInfo extends React.Component {

    getError = key => {
        const {errors} = this.props.ProfileUser.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    submit = () => {
        const {model} = this.props.ProfileUser

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    render() {

        const {model} = this.props.ProfileUser

        const {user} = this.props
        const isPartner = (user.partner.id!=null);
        var hasAccountAndCustomer

        if(isPartner){
            hasAccountAndCustomer = model.hasAccount && model.hasCustomer
        }else{
            hasAccountAndCustomer = (user.accountId !== null)
        }

        return <div className="card mb-3">

            <div className="card-header">
                <h4 className="m-0">{translator('navigation_payment_info')}</h4>
                <div>{translator('payment_info_description')}</div>
            </div>

            <div className="card-body">

                <div className="row mb-4">
                    <div className="col-12 col-md-6 offset-md-3">
                        <div className="row">

                            <div className="col-12 col-md-6">
                                <div className={"card shadow-sm m-2 " + (hasAccountAndCustomer
                                    ? "bgc-green-50 c-green-500"
                                    : "bgc-yellow-50 c-orange-500")}>
                                    <div className="card-body px-2">
                                        <div className="row no-gutters">
                                            <div className="col-auto">
                                                <i className="fa fa-2x fa-cc-stripe mx-2"/>
                                            </div>
                                            <div className="col text-center text-md-left pt-1">
                                                {hasAccountAndCustomer
                                                    ? translator('has_stripe_account')
                                                    : translator('no_stripe_account')}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-12 col-md-6 text-center text-md-left">
                                <a href={"https://dashboard.stripe.com/oauth/authorize?" + [
                                    'client_id=' + 'ca_DYNvHI8sq8b4y4EPWTzduyCGk8hAxhDO',
                                    /*+ AppParameters.payments.stripe.clientId,*/
                                    'state=' + model.id,
                                    'response_type=code',
                                    'scope=read_write',
                                    'redirect_uri='+window.location.protocol + "//" + window.location.host+'/oauth/stripe/callback'
                                ].join('&')} className={"btn btn-lg mt-3 " + (hasAccountAndCustomer
                                    ? "btn-outline-success"
                                    : "btn-success")}>
                                    <i className="fa fa-plus"/>&nbsp;{translator('partner_create_stripe_account_action')}
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PaymentInfo))