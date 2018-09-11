import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';

class PartnerEdit extends React.Component {

    componentWillMount() {

        this.props.dispatch(FetchItem())
    }

    submit = () => {
        const {model} = this.props.Profile

        this.props.dispatch(Save({
            id: model.id,
            provider: model.provider,
            accountId: model.accountId,
        }))
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeString = name => e => this.change(name, e.target.value)

    getError = key => {
        const {errors} = this.props.Profile.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    renderProviderBanner = () => {

        const {model} = this.props.Profile

        switch (model.provider) {
            case'stripe':
                return <div className="banner">
                    <h3>{translator('partner_create_stripe_account_title')}</h3>
                    <h4>{translator('partner_create_stripe_account_footer')}</h4>

                    <a href={"https://connect.stripe.com/express/oauth/authorize?" + [
                        'client_id=' + AppParameters.payments.stripe.secret,
                        'redirect_url=' + AppParameters.payments.stripe.redirectUrl,
                        'state=' + model.id
                    ].join('&')} target="_blank" className="btn btn-outline-success">
                        <i className="fa fa-plus"/>&nbsp;{translator('partner_create_stripe_account_action')}
                    </a>
                </div>
        }

        return null
    }

    render() {

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.Profile

        const hasAvatar = model.id && model.user && model.user.avatar

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row mb-3">
                <div className="col-12 col-lg-8">
                    <h4 className="page-title">{translator('navigation_profile')}</h4>
                </div>
                <div className="col-12 col-lg-4 text-right">

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

            <div className="row">
                <div className="col">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    <div className="row">

                        {hasAvatar
                            ? <div className="col-12 col-sm-4 col-md-3 col-lg-2">
                                <div className="img-container">
                                    <img src={model.user.avatar.url} className="img-fluid"/>
                                </div>
                            </div>
                            : null}

                        <div className={hasAvatar ? "col-12 col-sm-8 col-md-9 col-lg-10" : "col-12"}>
                            <div className="row">
                                <div className="col-12">
                                    <h3>{model.user ? model.user.name : ''}</h3>

                                    {model.country ?
                                        <h4><i className="fa fa-globe"/>&nbsp;{model.country.name}</h4> : null}

                                    {model.user && model.user.phone ?
                                        <h5><i className="fa fa-phone"/>&nbsp;{model.user.phone}</h5> : null}

                                    {model.user && model.user.email ? <h5>{model.user.email}</h5> : null}

                                    <p className="text-muted">{model.postalCodes}</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div className="row">
                        <div className="col-12 col-lg-6 offset-lg-3">
                            {model.id && !model.hasAccount
                                ? this.renderProviderBanner()
                                : <div className="form-group">
                                    <label className="required">{translator('partner_account_id')}</label>
                                    <div className="input-group">
                                        <div className="input-group-prepend">
                                            <span className="input-group-text">{model.provider}</span>
                                        </div>
                                        <input type="text" name="accountId" className="form-control"
                                               onChange={this.changeString('accountId')}
                                               value={model.accountId || ""}/>
                                        {this.getError('accountId')}
                                    </div>
                                </div>}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(PartnerEdit))
