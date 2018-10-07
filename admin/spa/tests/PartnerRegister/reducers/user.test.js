import reducer from '../../../src/PartnerRegister/reducers/user'

describe('`PartnerRegister` user reducer', () => {

    it('`id` should return the initial state', () => {
        expect(reducer(undefined, {}).id).toEqual(null)
    })

    it('`email` should return the initial state', () => {
        expect(reducer(undefined, {}).email).toEqual(null)
    })

    it('`phone` should return the initial state', () => {
        expect(reducer(undefined, {}).phone).toEqual(null)
    })

    it('`name` should return the initial state', () => {
        expect(reducer(undefined, {}).name).toEqual(null)
    })

    it('`password` should return the initial state', () => {
        expect(reducer(undefined, {}).password).toEqual(null)
    })

    it('`password2` should return the initial state', () => {
        expect(reducer(undefined, {}).password2).toEqual(null)
    })
})