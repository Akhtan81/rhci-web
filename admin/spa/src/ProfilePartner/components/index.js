import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import StripeCheckout from 'react-stripe-checkout';

import selectors from './selectors';
import FetchItem from '../actions/FetchItem';
import Save from '../actions/Save';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";
import Subscriptions from "./Subscriptions";
import {MODEL_CHANGED} from "../actions";

class ProfilePartner extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_profile'))

        this.props.dispatch(FetchItem())
    }

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

    changeString = name => e => this.change(name, e.target.value)

    onCardTokenReady = cardToken => {

        if (!cardToken) return;

        this.props.dispatch(Save({
            ...this.props.ProfilePartner.model,
            cardToken: cardToken.id,
            cardTokenResponse: JSON.stringify(cardToken)
        }))
    }

    renderProviderBanner = () => {

        const {model} = this.props.ProfilePartner

        if (!model.id) return null;

        switch (model.provider) {
            case 'stripe':
                return <div className="banner">
                    <h3>{translator('partner_create_stripe_account_title')}</h3>
                    <h4>{translator('partner_create_stripe_account_footer')}</h4>

                    <a href={"https://dashboard.stripe.com/oauth/authorize?" + [
                        'client_id=' + AppParameters.payments.stripe.clientId,
                        'state=' + model.id,
                        'response_type=code',
                        'scope=read_write'
                    ].join('&')} className="btn btn-success">
                        <i className="fa fa-plus"/>&nbsp;{translator('partner_create_stripe_account_action')}
                    </a>
                </div>
        }

        return null
    }

    renderProviderCardBanner = () => {

        const {model, isLoading} = this.props.ProfilePartner

        if (!model.id) return null;

        switch (model.provider) {
            case 'stripe':
                return <div className="banner">
                    <h3>{translator('partner_create_stripe_card_title')}</h3>
                    <h4>{translator('partner_create_stripe_card_footer')}</h4>

                    <StripeCheckout
                        email={model.user.email}
                        token={this.onCardTokenReady}
                        stripeKey={AppParameters.payments.stripe.storeSecret}>
                        <button className="btn btn-success btn-sm"
                                disabled={isLoading}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-credit-card"}/>
                            &nbsp;{translator('partner_create_stripe_card_action')}
                        </button>
                    </StripeCheckout>
                </div>
        }

        return null
    }

    renderAssignedPostalCodes() {

        const {model} = this.props.ProfilePartner

        return <div className="row">
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}</h5>
                {model.postalCodesJunkRemoval.length > 0
                    ? <ul>{model.postalCodesJunkRemoval.map((item, i) =>
                        <li key={i}>{item}</li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}</h5>
                {model.postalCodesRecycling.length > 0
                    ? <ul>{model.postalCodesRecycling.map((item, i) =>
                        <li key={i}>{item}</li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
            <div className="col-12 col-md-4">
                <h5><i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}</h5>
                {model.postalCodesShredding.length > 0
                    ? <ul>{model.postalCodesShredding.map((item, i) =>
                        <li key={i}>{item}</li>)}
                    </ul>
                    : <span>{translator('no_assigned_postal_codes')}</span>}
            </div>
        </div>
    }

    renderPaymentCredentials = () => {

        const {model} = this.props.ProfilePartner

        if (!model.id) return null;

        if (!model.hasAccount) {
            return this.renderProviderBanner()
        }

        if (!model.hasCard) {
            return this.renderProviderCardBanner()
        }

        const hasAccountAndCustomer = model.hasAccount && model.customerId

        return <div>

            <h4>{translator('partner_payment_credentials')}</h4>

            <div className="card-deck mx-0 text-center">
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
                <div className="card shadow-sm m-2 bgc-green-50 c-green-500">
                    <div className="card-body px-2">
                        <div className="row no-gutters">
                            <div className="col-auto">
                                <i className="fa fa-2x fa-credit-card mx-2"/>
                            </div>
                            <div className="col text-center text-md-left pt-1">
                                {translator('has_stripe_card')}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    }

    renderProfileForm = () => {

        const {model} = this.props.ProfilePartner

        return <div className="row">
            <div className="col-12">
                <div className="row">
                    <div className="col-12">
                        <div className="form-group">
                            <label className="required">{translator('name')}</label>
                            <input type="text"
                                   name="name"
                                   className="form-control"
                                   onChange={this.changeString('name')}
                                   value={model.user.name || ''}/>
                            {this.getError('name')}
                        </div>
                    </div>
                    <div className="col">
                        <div className="form-group">
                            <label className="required">{translator('email')}</label>
                            <input type="email"
                                   name="email"
                                   className="form-control"
                                   onChange={this.changeString('email')}
                                   value={model.user.email || ''}/>
                            {this.getError('email')}
                        </div>
                    </div>
                    <div className="col">

                        <div className="form-group">
                            <label>{translator('phone')}</label>
                            <input type="text"
                                   name="phone"
                                   className="form-control"
                                   onChange={this.changeString('phone')}
                                   value={model.user.phone || ''}/>
                            {this.getError('phone')}
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label className={!model.id ? "required" : ""}>{translator('current_password')}</label>
                            <input type="password"
                                   name="currentPassword"
                                   className="form-control"
                                   onChange={this.changeString('currentPassword')}
                                   value={model.user.currentPassword || ''}/>
                            {this.getError('currentPassword')}
                        </div>

                    </div>
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label className={!model.id ? "required" : ""}>{translator('password')}</label>
                            <input type="password"
                                   name="password"
                                   className="form-control"
                                   onChange={this.changeString('password')}
                                   value={model.user.password || ''}/>
                            {this.getError('password')}
                        </div>

                    </div>
                    <div className="col-12 col-sm-4">

                        <div className="form-group">
                            <label
                                className={!model.id ? "required" : ""}>{translator('password_repeat')}</label>
                            <input type="password"
                                   name="password2"
                                   className="form-control"
                                   onChange={this.changeString('password2')}
                                   value={model.user.password2 || ''}/>
                            {this.getError('password2')}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    }

    render() {

        const {model, isSaveSuccess, serverErrors, isLoading, isValid} = this.props.ProfilePartner

        let location = ''
        if (model.location) {
            const items = []
            if (model.location.city) {
                items.push(model.location.city);
            }
            if (model.location.address) {
                items.push(model.location.address);
            }
            if (model.location.postalCode) {
                items.push(model.location.postalCode);
            }

            location = items.join(', ')
        }

        return <div className="bgc-white bd bdrs-3 p-20 my-3">

            <div className="row mb-3">
                <div className="col-12 col-lg-6">
                    <h4 className="page-title">{translator('navigation_profile')}</h4>
                </div>
                <div className="col-12 col-lg-6 text-right">

                    {model.user ? <StripeCheckout
                        email={model.user.email}
                        token={this.onCardTokenReady}
                        stripeKey={AppParameters.payments.stripe.storeSecret}>
                        <button className="btn btn-outline-success btn-sm mr-1"
                                disabled={isLoading}>
                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-plus"}/>
                            &nbsp;{translator('partner_create_stripe_card_action')}
                        </button>
                    </StripeCheckout> : null}

                    {model.accountId ? <a href={"https://dashboard.stripe.com/oauth/authorize?" + [
                        'client_id=' + AppParameters.payments.stripe.clientId,
                        'state=' + model.id,
                        'response_type=code',
                        'scope=read_write'
                    ].join('&')} className={"btn btn-outline-success btn-sm mr-1" + (isLoading ? " disabled": "")}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-plus"}/>
                        &nbsp;{translator('partner_create_stripe_account_action')}
                    </a> : null}

                    <button className="btn btn-success btn-sm"
                            disabled={!isValid || isLoading}
                            onClick={this.submit}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                        &nbsp;{translator('save')}
                    </button>

                    {isSaveSuccess && <div className="text-muted c-green-500">
                        <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                    </div>}
                </div>
            </div>

            <div className="row mb-4">
                <div className="col">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    <div className="row mb-4">

                        <div className="col-12">
                            <div className="row">
                                <div className="col-12 col-md-6 col-lg-7">
                                    {this.renderProfileForm()}

                                    {model.country ?
                                        <h4><i className="fa fa-globe"/>&nbsp;{model.country.name}</h4> : null}

                                    {location ?
                                        <h4><i className="fa fa-map-marker"/>&nbsp;{location}</h4> : null}
                                </div>
                                <div className="col-12 col-md-6 col-lg-5">
                                    {this.renderPaymentCredentials()}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="row">
                        <div className="col-12">
                            <div className="row">
                                <div className="col-12 col-md-3">
                                    <h4>{translator('requested_postal_codes')}</h4>
                                    {model.requests.length > 0
                                        ? <ul>{model.requests.map((item, i) =>
                                            <li key={i}>{item.postalCode} - {translator('order_types_' + item.type)}</li>)}
                                        </ul>
                                        : <span>{translator('no_requested_postal_codes')}</span>}
                                </div>
                                <div className="col-12 col-md-9">
                                    <h4>{translator('assigned_postal_codes')}</h4>
                                    {this.renderAssignedPostalCodes()}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <Subscriptions/>
        </div>
    }
}

export default withRouter(connect(selectors)(ProfilePartner))
