# Image-Downsampling
We develop a new algorithm for image downsampling by employing a well-known method in psychology known as reverse correlation.

The project consists of 3 experiments:
1. Generating Image Dataset: First, we create a web interface for generating images using reverse correlation. For each full-sized image in our dataset, we generate a downsampled version of the image using a standard method (for example, Lanczos). We then add random noise to parts of the image and create pairs out of these random noise-added downsized images. We then ask the user to select the image which better resembles the original one. After receiving about 300 responses for each image, we take the average of the users' choices and these constitute our labels for the image dataset.

Here is the link to the web interface: http://users.cs.cf.ac.uk/NakumG/image_select.php

The directory "web interface 1" contains the code for this interface.


2. Comparing Reverse Correlation with Standard Methods: We compare a randomly chosen subset of the images obtained from experiment 1 with those obtained from existing standard methods for image downsizing - DPID, Pixelated Abstraction and Lanczos. We found that for 70% of the comapred images, our method achieves better performance than the other methods.

The directory "web interface 2" contains the code for the interface designed to test the new method.

3. Learning an Algorithm to Generate Reverse Correlation Images: We train a cGAN to learn the mapping from full-sized images to downsized images obtained by reverse correlation.

The directory "pix2pix" contains the code for learning this mapping.

References:
A. Reverse Correlation:
1. L. Brinkman, A. Todorov & R. Dotsch (2017) Visualising mental representations: A primer on noise-based reverse correlation in social psychology, European Review of Social Psychology, 28:1, 333-361, https://doi.org/10.1080/10463283.2017.1381469

2. Richard F.Murray & Jason M.Gold (2004) Troubles with bubbles, https://doi.org/10.1016/j.visres.2003.10.006

3. Paul L.Rosin & Yu-Kun Lai (2013) Artistic minimal rendering with lines and blocks, https://doi.org/10.1016/j.gmod.2013.03.004

4. Joshua Conrad Jackson , Neil Hester, Kurt Gray (2018) The faces of God in America: Revealing religious diversity across people and politics, https://doi.org/10.1371/journal.pone.0198745

5. Peter Scarfe, Paul B.Hibbard (2013) Reverse correlation reveals how observers sample visual information when estimating three-dimensional shape, https://doi.org/10.1016/j.visres.2013.04.016

6. Huanping Dai and Christophe Micheyl (2010) Psychophysical Reverse Correlation with Multiple Response Alternatives, doi:  10.1037/a0017171
https://www.ncbi.nlm.nih.gov/pmc/articles/PMC3158580/

7. 


B. Learning:
1. Phillip Isola, Jun-Yan Zhu, Tinghui Zhou, Alexei A. Efros (2016) Image-to-Image Translation with Conditional Adversarial Networks, https://arxiv.org/abs/1611.07004

2. Vinson Luo, Michael Straka, Lucy Li - Historical and Modern Image-to-Image Translation with Generative Adversarial Networks, https://web.stanford.edu/~lucy3/CS231N.pdf

3. Leon A. Gatys, Alexander S. Ecker, Matthias Bethge (2016) Image Style Transfer Using Convolutional Neural Networks, https://ieeexplore.ieee.org/document/7780634/

4. Justin Johnson, Alexandre Alahi, and Li Fei-Fei - Perceptual Losses for Real-Time Style Transfer and Super-Resolution, https://arxiv.org/abs/1603.08155

5. Jiwon Kim, Jung Kwon Lee and Kyoung Mu Lee (2015) Accurate Image Super-Resolution Using Very Deep Convolutional Networks, https://arxiv.org/abs/1511.04587

6. Olaf Ronneberger, Philipp Fischer, Thomas Brox (2015) U-Net: Convolutional Networks for Biomedical Image Segmentation, https://arxiv.org/abs/1505.04597

7. Dong C., Loy C.C., He K., Tang X. (2014) Learning a Deep Convolutional Network for Image Super-Resolution. In: Fleet D., Pajdla T., Schiele B., Tuytelaars T. (eds) Computer Vision – ECCV 2014. ECCV 2014. Lecture Notes in Computer Science, vol 8692. Springer, Cham
https://link.springer.com/chapter/10.1007/978-3-319-10593-2_13

8. Xianxu Hou, Jiang Duan, Guoping Qiu (2017)  Deep Feature Consistent Deep Image Transformations: Downscaling, Decolorization and HDR Tone Mapping, https://arxiv.org/abs/1707.09482

9. Y. Li et al., "Convolutional Neural Network-Based Block Up-sampling for Intra Frame Coding," in IEEE Transactions on Circuits and Systems for Video Technology, doi: 10.1109/TCSVT.2017.2727682
https://ieeexplore.ieee.org/document/7982641/
