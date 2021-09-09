import { assert } from 'chai'
import fakeDom from 'helpers/fakeDom'
import injectFixture from 'helpers/injectFixture'

describe('modules/system/assets/js/framework/base.js', function () {
    describe('The Winter JS Framework', function () {
        let dom,
            window

        // Set a reasonable timeout
        this.timeout(2000)

        beforeEach((done) => {
            // Load base framework in a fake DOM
            dom = fakeDom(`
                <script src="file://./modules/system/assets/js/framework/base.js" id="frameworkScript"></script>
            `)

            window = dom.window
            window.frameworkScript.onload = function () {
                done()
            }
        })

        afterEach(() => {
            window.close()
        })

        it('initialises correctly', function () {
            assert.exists(window.winter)
            assert.equal('object', typeof window.winter)
            assert.exists(window.winter.extend)
        })

        it('can load a module', function () {
            injectFixture(dom, 'framework/TestModule.js')

            assert.isTrue(window.winter.hasModule('TestModule'))
        })
    })
})
