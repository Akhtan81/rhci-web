import reducer from '../../../src/PartnerRegister/reducers'

describe('`PartnerRegister` index reducer', () => {

    it('`serverErrors` should return the initial state', () => {
        expect(reducer(undefined, {}).serverErrors).toEqual([])
    })

    it('`isSaveSuccess` should return the initial state', () => {
        expect(reducer(undefined, {}).isSaveSuccess).toEqual(false)
    })

    it('`isValid` should return the initial state', () => {
        expect(reducer(undefined, {}).isValid).toEqual(false)
    })

    it('`isLoading` should return the initial state', () => {
        expect(reducer(undefined, {}).isLoading).toEqual(false)
    })

    it('`validator` should return the initial state', () => {
        expect(reducer(undefined, {}).validator).toEqual({
            count: 0,
            messages: [],
            errors: {}
        })
    })

    it('`changes` should return the initial state', () => {
        expect(reducer(undefined, {}).changes).toEqual({})
    })
})