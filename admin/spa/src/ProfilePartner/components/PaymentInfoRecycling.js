import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import StripeCheckout from 'react-stripe-checkout';

import selectors from './selectors';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {MODEL_CHANGED} from "../actions";
import Subscriptions from "./Subscriptions";
import AddSubscription from "../actions/AddSubscription";

class PaymentInfoRecycling extends React.Component {

    getError = key => {
        const {errors} = this.props.ProfilePartner.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    submit = () => {
        const {model} = this.props.ProfilePartner

        this.props.dispatch(Save(model))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    onCardTokenReady = cardToken => {

        if (!cardToken) return;

        this.props.dispatch(Save({
            ...this.props.ProfilePartner.model,
            cardToken: cardToken.id,
            cardTokenResponse: JSON.stringify(cardToken)
        }, () => {
            this.props.dispatch(AddSubscription())
        }))
    }

    render() {

        const {model, isLoading} = this.props.ProfilePartner

        return <div className="bgc-white bd bdrs-3 p-20 my-3">

            <div className="row mb-3">
                <div className="col-12">
                    <h4 className="page-title">{translator('navigation_payment_info_recycling')}</h4>
                    <p>{translator('payment_info_recycling_description')}</p>
                    <h5 className="text-center my-3">{translator('payment_info_recycling_cost')}</h5>
                </div>
            </div>

            <div className="row mb-4">
                <div className="col-12 col-md-6 offset-md-3">
                    <div className="row">
                        <div className="col-12 col-md-6">
                            <div className={"card shadow-sm m-2 " + (model.hasCard
                                ? "bgc-green-50 c-green-500"
                                : "bgc-yellow-50 c-orange-500")}>
                                <div className="card-body px-2">
                                    <div className="row no-gutters">
                                        <div className="col-auto">
                                            <i className="fa fa-2x fa-credit-card mx-2"/>
                                        </div>
                                        <div className="col text-center text-md-left pt-1">
                                            {model.hasCard
                                                ? translator('has_stripe_card')
                                                : translator('no_stripe_card')}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div className="col-12 col-md-6 text-center text-md-left">
                            {model.user ? <StripeCheckout
                                email={model.user.email}
                                token={this.onCardTokenReady}
                                stripeKey={AppParameters.payments.stripe.storeSecret}>
                                <button className={"btn btn-lg mt-3 " + (model.hasCard
                                    ? "btn-outline-success"
                                    : "btn-success")} disabled={isLoading}>
                                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-plus"}/>
                                    &nbsp;{translator('partner_create_stripe_card_action')}
                                </button>
                            </StripeCheckout> : null}
                        </div>
                    </div>
                </div>
            </div>

            <Subscriptions/>

        </div>
    }
}

export default withRouter(connect(selectors)(PaymentInfoRecycling))
