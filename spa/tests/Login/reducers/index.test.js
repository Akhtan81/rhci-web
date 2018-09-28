import reducer from '../../../src/Login/reducers'

describe('`Login` index reducer', () => {

    it('`login` should return the initial state', () => {
        expect(reducer(undefined, {}).login).toEqual(null)
    })

    it('`password` should return the initial state', () => {
        expect(reducer(undefined, {}).password).toEqual(null)
    })

    it('`errors` should return the initial state', () => {
        expect(reducer(undefined, {}).errors).toEqual([])
    })

    it('`isValid` should return the initial state', () => {
        expect(reducer(undefined, {}).isValid).toEqual(false)
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isValid).toEqual(false)
    })

})