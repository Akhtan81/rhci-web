import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import {dateFormat} from "../../Common/utils";

class Chat extends React.Component {

    render() {

        const {model} = this.props.OrderEdit

        let messages = model.messages
        model.items.forEach(item => {
            if (item.message) {
                messages.push(item.message)
            }
        })

        let currentSender = null

        return <div className="bd bgc-white mb-3">
            <div className="layers">
                <div className="layer w-100 p-20">
                    <h5 className="lh-1">{translator('order_messages')}</h5>
                </div>
                <div className="layer w-100">
                    <div className="bgc-grey-200 p-20" style={{maxHeight: '500px'}}>
                        {messages.map((item, i) => {

                            const canShowUsername = item.user.id !== currentSender

                            currentSender = item.user.id

                            return <div key={i} className="peer-group">
                                <div className="peers mb-2">
                                    {canShowUsername && item.user.avatar ?
                                        <div className="peer mr-1">
                                            <img className="w-2r bdrs-50p" src={item.user.avatar.url}/>
                                        </div> : null}
                                    <div className="peer w-75 peer-greed">
                                        <div className="layers ai-fs gapY-5">
                                            <div className="layer">
                                                <div
                                                    className="peers ai-c pY-3 pX-10 bgc-white bdrs-2">
                                                    {canShowUsername && <div className="peer mR-10 w-100">
                                                        <small>{item.user.name}</small>
                                                    </div>}
                                                    <div className="peer-greed w-100">
                                                        <span>{item.text}</span>
                                                    </div>
                                                    <div className="peer mR-10 w-100">
                                                        <small>{dateFormat(item.createdAt)}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {item.media.map((media, j) => {
                                    return <div key={j} className="peers mb-2">
                                        <div className="peer peer-greed">
                                            <div className="layers ai-fs gapY-5">
                                                <div className="layer">
                                                    <a href={media.url} download={true} className="peers ai-c pY-3 pX-10 bgc-white bdrs-2">
                                                        <div className="peer mR-10 w-100">
                                                            <small>{media.name}</small>
                                                        </div>
                                                        <div className="peer-greed w-100">
                                                            <img src={media.url} className="img-fluid"/>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                })}
                            </div>
                        })}
                    </div>
                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(Chat))
