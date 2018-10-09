import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import selectors from './selectors';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import {setTitle} from "../../Common/utils";

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

    renderProviderBanner = () => {

        const {model} = this.props.ProfilePartner

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
                    ].join('&')} className="btn btn-outline-success">
                        <i className="fa fa-plus"/>&nbsp;{translator('partner_create_stripe_account_action')}
                    </a>
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

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.ProfilePartner

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
                <div className="col-12 col-lg-8">
                    <h4 className="page-title">{translator('navigation_profile')}</h4>
                </div>
                <div className="col-12 col-lg-4 text-right">

                    {model.accountId ? <a href={"https://dashboard.stripe.com/oauth/authorize?" + [
                        'client_id=' + AppParameters.payments.stripe.clientId,
                        'state=' + model.id,
                        'response_type=code',
                        'scope=read_write'
                    ].join('&')} className="btn btn-outline-success btn-sm">
                        <i className="fa fa-plus"/>&nbsp;{translator('partner_create_stripe_account_action')}
                    </a> : null}

                    {isSaveSuccess && <div className="text-muted c-green-500">
                        <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                    </div>}
                </div>
            </div>

            <div className="row">
                <div className="col">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    <div className="row mb-4">

                        <div className="col-12">
                            <div className="row">
                                <div className="col-12 col-md-6">
                                    {model.user ? <h3>{model.user.name}</h3> : null}

                                    {model.country ?
                                        <h4><i className="fa fa-globe"/>&nbsp;{model.country.name}</h4> : null}

                                    {location ?
                                        <h4><i className="fa fa-map-marker"/>&nbsp;{location}</h4> : null}

                                    {model.user && model.user.phone
                                        ? <h5><i className="fa fa-phone"/>&nbsp;{model.user.phone}</h5>
                                        : null}

                                    {model.user && model.user.email
                                        ? <h5><i className="fa fa-at"/>&nbsp;{model.user.email}</h5>
                                        : null}

                                    {model.user && model.hasAccount
                                        ? <h5 className="c-green-500"><i
                                            className="fa fa-check"/>&nbsp;{translator('has_stripe_account')}</h5>
                                        : null}

                                </div>
                                <div className="col-12 col-md-6">
                                    {model.id && !model.hasAccount
                                        ? this.renderProviderBanner()
                                        : null}
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
        </div>
    }
}

export default withRouter(connect(selectors)(ProfilePartner))
